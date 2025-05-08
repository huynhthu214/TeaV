<?php
$namePage = "Blog";
include "view/header.php";

$conn = mysqli_connect("localhost", "root", "", "teav_shop1");

if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
    
    // Lấy ID bài viết từ tham số URL
    $blogId = isset($_GET['id']) ? $_GET['id'] : '';
    
    // Kiểm tra nếu id không tồn tại
    if (empty($blogId)) {
        header("Location: blog.php");
        exit;
    }
    
    // Lấy thông tin chi tiết bài viết
    $blogQuery = "SELECT b.BlogId, b.Title, b.DateUpload, b.ImgLink, b.Summary, b.Email, b.Content 
                FROM Blog b 
                WHERE b.BlogId = ? AND b.IsShow = 'Yes'";
    
    $stmt = $conn->prepare($blogQuery);
    $stmt->bind_param("s", $blogId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header("Location: blog.php");
        exit;
    }
    
    $blog = $result->fetch_assoc();
    
    // Lấy tags của bài viết
    $blogTagQuery = "SELECT t.Name 
                    FROM Tag t 
                    INNER JOIN BlogTag bt ON t.TagId = bt.TagId 
                    WHERE bt.BlogId = ?";
    
    $tagStmt = $conn->prepare($blogTagQuery);
    $tagStmt->bind_param("s", $blogId);
    $tagStmt->execute();
    $tagResult = $tagStmt->get_result();
    
    $blogTags = [];
    while ($blogTag = $tagResult->fetch_assoc()) {
        $blogTags[] = $blogTag['Name'];
    }
    
    // Format ngày
    $date = new DateTime($blog['DateUpload']);
    $formattedDate = $date->format('F j, Y');
    
    // Truy vấn để lấy các bài viết liên quan (đơn giản lấy 3 bài khác)
    $relatedQuery = "SELECT BlogId, Title, ImgLink 
                    FROM Blog 
                    WHERE BlogId != ? AND IsShow = 'Yes' 
                    LIMIT 3";
    
    $relatedStmt = $conn->prepare($relatedQuery);
    $relatedStmt->bind_param("s", $blogId);
    $relatedStmt->execute();
    $relatedResult = $relatedStmt->get_result();
    
    $relatedPosts = [];
    while ($post = $relatedResult->fetch_assoc()) {
        $relatedPosts[] = $post;
    }
?>

<main>
  <section class="blog-detail py-5">
    <div class="container">
      <div class="row" style="justify-content: center">
        <div class="col-sm-10 px-5">
          <article class="blog-post">
            <h1><?php echo htmlspecialchars($blog['Title']); ?></h1>
            <p class="text-muted">Posted date <?php echo $formattedDate; ?> by <?php echo htmlspecialchars($blog['Email']); ?></p>
            
            <?php if (!empty($blogTags)): ?>
            <div class="tags mb-3">
              <?php foreach ($blogTags as $tag): ?>
                <span class="badge bg-secondary"><?php echo htmlspecialchars($tag); ?></span>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <img
              src="<?php echo htmlspecialchars($blog['ImgLink']); ?>"
              class="img-fluid mb-4"
              alt="<?php echo htmlspecialchars($blog['Title']); ?>"
            />
            
            <div class="blog-content">
              <?php echo $blog['Content'];?>
            </div>
            
            <div class="share-buttons mt-4">
              <a
                href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                target="_blank"
                class="btn btn-sm btn-primary-blog"
                ><i class="fab fa-facebook-f"></i> Share</a
              >
              <a
                href="https://x.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($blog['Title']); ?>"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            
            <div class="comment-section mt-5">
              <h4>Comments</h4>
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
                  Post comments
                </button>
              </form>
            </div>
          </article>
          
          <!-- Bài viết liên quan -->
          <div class="related-posts mt-5">
            <h3>Related post</h3>
            <div class="row">
              <?php foreach ($relatedPosts as $post): ?>
                <div class="col-md-4 mb-4">
                  <div class="card h-100">
                    <img 
                      src="<?php echo htmlspecialchars($post['ImgLink']); ?>" 
                      class="card-img-top" 
                      alt="<?php echo htmlspecialchars($post['Title']); ?>"
                    >
                    <div class="card-body">
                      <h5 class="card-title"><?php echo htmlspecialchars($post['Title']); ?></h5>
                      <a href="blog_detail.php?id=<?php echo $post['BlogId']; ?>" class="btn btn-info btn-sm">Read more</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          
          <div class="mt-4">
            <a href="blog.php" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left"></i> Back to blog
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<?php 
$stmt->close();
$tagStmt->close();
$relatedStmt->close();
$conn->close();

// Include footer
include "view/footer.php";
?>