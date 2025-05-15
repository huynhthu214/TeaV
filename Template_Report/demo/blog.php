<?php
$namePage = "Blog";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
    
    // Lấy danh sách các tag
    $tagQuery = "SELECT DISTINCT t.TagId, t.Name 
                FROM Tag t 
                INNER JOIN BlogTag bt ON t.TagId = bt.TagId";
    $tagResult = $conn->query($tagQuery);
    $tags = [];
    while ($tag = $tagResult->fetch_assoc()) {
        $tags[] = $tag;
    }
    
    // Xử lý filter nếu có
    $whereClause = "WHERE b.IsShow = 'Yes'";
    $tagFilter = isset($_GET['tag']) ? $_GET['tag'] : '';
    
    if (!empty($tagFilter)) {
        $whereClause .= " AND EXISTS (SELECT 1 FROM BlogTag bt WHERE bt.BlogId = b.BlogId AND bt.TagId = '$tagFilter')";
    }
    
    // Lấy danh sách blog với filter
    $blogQuery = "SELECT b.BlogId, b.Title, b.DateUpload, b.ImgLink, b.Summary, b.Email, b.Content 
                FROM Blog b 
                $whereClause 
                ORDER BY b.DateUpload DESC";
    $blogResult = $conn->query($blogQuery);
    
    // Truy vấn để lấy các bài viết phổ biến (đơn giản lấy 3 bài đầu tiên)
    $popularQuery = "SELECT BlogId, Title, ImgLink FROM Blog WHERE IsShow = 'Yes' LIMIT 3";
    $popularResult = $conn->query($popularQuery);
    $popularPosts = [];
    while ($post = $popularResult->fetch_assoc()) {
        $popularPosts[] = $post;
    }
?>

<main>
  <section class="blog py-5">
    <div class="container">
      <h1 class="text-center mb-4">Blog</h1>
      <div class="row" style="justify-content: center">
        <div class="col-sm-10 px-5">
          
          <?php 
          // Hiển thị các bài viết blog
          if ($blogResult->num_rows > 0) {
              while ($blog = $blogResult->fetch_assoc()) {
                  // Lấy tags của bài viết
                  $blogTagQuery = "SELECT t.Name 
                                  FROM Tag t 
                                  INNER JOIN BlogTag bt ON t.TagId = bt.TagId 
                                  WHERE bt.BlogId = '{$blog['BlogId']}'";
                  $blogTagResult = $conn->query($blogTagQuery);
                  $blogTags = [];
                  while ($blogTag = $blogTagResult->fetch_assoc()) {
                      $blogTags[] = $blogTag['Name'];
                  }
                  
                  // Format ngày
                  $date = new DateTime($blog['DateUpload']);
                  $formattedDate = $date->format('F j, Y');
                  
          ?>
          <article class="blog-post">
            <h2><?php echo htmlspecialchars($blog['Title']); ?></h2>
            <p class="text-muted">Posted date <?php echo $formattedDate; ?> by <?php echo htmlspecialchars($blog['Email']); ?></p>
            
            <?php if (!empty($blogTags)): ?>
            <div class="tags mb-2">
              <?php foreach ($blogTags as $tag): ?>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($tag); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <img
              src="<?php echo htmlspecialchars($blog['ImgLink']); ?>"
              class="img-fluid-blog mb-3"
              alt="<?php echo htmlspecialchars($blog['Title']); ?>"
            />
            <p><?php echo htmlspecialchars($blog['Summary']); ?></p>
            <a href="blog_detail.php?id=<?php echo $blog['BlogId']; ?>" class="read-more">Read more</a>
            
            <div class="share-buttons">
              <a
                href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/sblog_detail.php?id=' . $blog['BlogId']); ?>"
                target="_blank"
                class="btn btn-sm btn-primary-blog"
                ><i class="fab fa-facebook-f"></i>Share</a
              >
              <a
                href="https://x.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/sblog_detail.php?id=' . $blog['BlogId']); ?>&text=<?php echo urlencode($blog['Title']); ?>"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            
            <div class="comment-section">
              <h5>Comments</h5>
              <div id="comments-<?php echo $blog['BlogId']; ?>" class="mb-3"></div>
              <form class="comment-form" data-post="<?php echo $blog['BlogId']; ?>">
                <div class="mb-3">
                  <textarea
                    class="form-control"
                    rows="3"
                    placeholder="Your comment..."
                    required
                  ></textarea>
                </div>
                <button type="submit" class="btn btn-success">
                  Post a comment
                </button>
              </form>
            </div>
          </article>
          <?php
              }
          } else {
              echo '<div class="alert alert-info">Không có bài viết nào phù hợp với tiêu chí tìm kiếm.</div>';
          }
          ?>
        </div>
        
        <div class="col-sm-2 ps-5">
          <div class="sidebar">
            <h3>Category</h3>
            <ul>
              <?php foreach ($tags as $tag): ?>
                <li>
                  <a href="?tag=<?php echo $tag['TagId']; ?>">
                    <?php echo htmlspecialchars($tag['Name']); ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
            
            <h3>Recent posts</h3>
            <ul>
              <?php
              // Reset con trỏ kết quả để sử dụng lại $blogResult
              $blogResult->data_seek(0);
              $count = 0;
              while ($blog = $blogResult->fetch_assoc()) {
                  if ($count >= 3) break; // Chỉ hiển thị 3 bài viết gần đây
                  echo '<li><a href="sblog_detail.php?id=' . $blog['BlogId'] . '">' . htmlspecialchars($blog['Title']) . '</a></li>';
                  $count++;
              }
              ?>
            </ul>
            
            <h3>Popular post</h3>
            <ul>
              <?php foreach ($popularPosts as $post): ?>
                <li>
                  <div class="row">
                    <div class="col-4">
                      <a href="sblog-detail.php?id=<?php echo $post['BlogId']; ?>">
                        <img src="<?php echo htmlspecialchars($post['ImgLink']); ?>" alt="" class="img-fluid-blog-min mb-3">
                      </a>
                    </div>
                    <div class="col-8">
                      <a href="sblog-detail.php?id=<?php echo $post['BlogId']; ?>" class="min-title">
                        <?php echo htmlspecialchars($post['Title']); ?>
                      </a>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php 
// Đóng kết nối
$conn->close();

// Include footer
include "view/footer.php";
?>