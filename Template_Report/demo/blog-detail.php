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

// Hàm tạo ID ngẫu nhiên cho bình luận
function generateReactionId() {
    return 'R' . rand(1000000, 9999999);
}

// Xử lý khi người dùng gửi bình luận
if (isset($_POST['submit_comment'])) {
    $email = $_POST['email'];
    $comment = $_POST['comment'];
    $blog_id = $_POST['blog_id'];
    
    // Kiểm tra dữ liệu
    if (!empty($email) && !empty($comment) && !empty($blog_id)) {
        // Tạo ReactionId mới
        $reactionId = generateReactionId();
        
        // Mặc định IsShow = 'No', admin sẽ duyệt sau
        $commentQuery = "INSERT INTO Reaction (ReactionId, Email, BlogId, Comment, IsShow) 
                        VALUES (?, ?, ?, ?, 'No')";
        
        $commentStmt = $conn->prepare($commentQuery);
        $commentStmt->bind_param("ssss", $reactionId, $email, $blog_id, $comment);
        $result = $commentStmt->execute();
        
        if ($result) {
            echo "<script>alert('Cảm ơn bạn đã bình luận. Bình luận sẽ được hiển thị sau khi được duyệt.');</script>";
        } else {
            echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại sau.');</script>";
        }
        $commentStmt->close();
    }
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

// Lấy bình luận đã được duyệt cho bài viết
$commentsQuery = "SELECT r.ReactionId, r.Email, r.Comment, a.FullName 
                FROM Reaction r 
                LEFT JOIN Account a ON r.Email = a.Email 
                WHERE r.BlogId = ? AND r.IsShow = 'Yes' 
                ORDER BY r.ReactionId DESC";

$commentsStmt = $conn->prepare($commentsQuery);
$commentsStmt->bind_param("s", $blogId);
$commentsStmt->execute();
$commentsResult = $commentsStmt->get_result();

$comments = [];
while ($comment = $commentsResult->fetch_assoc()) {
    $comments[] = $comment;
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
                ><i class="fab fa-facebook-f"></i> Chia sẻ</a
              >
              <a
                href="https://x.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($blog['Title']); ?>"
                target="_blank"
                class="btn btn-sm btn-info"
                ><i class="fab fa-twitter"></i> Tweet</a
              >
            </div>
            
            <div class="comment-section mt-5">
              <h4>Bình luận</h4>
              
              <!-- Hiển thị danh sách bình luận đã được duyệt -->
              <div class="comments-list mb-4">
                <?php if (count($comments) > 0): ?>
                  <?php foreach ($comments as $comment): ?>
                    <div class="comment-item p-3 mb-2 border-bottom">
                      <div class="comment-header">
                        <strong>
                          <?php echo htmlspecialchars($comment['FullName'] ? $comment['FullName'] : $comment['Email']); ?>
                        </strong>
                      </div>
                      <div class="comment-content mt-2">
                        <?php echo nl2br(htmlspecialchars($comment['Comment'])); ?>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <p>Chưa có bình luận nào.</p>
                <?php endif; ?>
              </div>
              
              <!-- Form để người dùng gửi bình luận -->
              <h4>Để lại bình luận</h4>
              <form method="POST">
                  <input type="hidden" name="blog_id" value="<?php echo $blog['BlogId']; ?>">
                  
                  <div class="form-group mb-3">
                      <input type="email" name="email" class="form-control" required placeholder="Email của bạn">
                  </div>
                  
                  <div class="form-group mb-3">
                      <textarea name="comment" class="form-control" rows="3" required placeholder="Nội dung bình luận"></textarea>
                  </div>
                  
                  <button type="submit" name="submit_comment" class="btn btn-primary">Gửi bình luận</button>
              </form>
            </div>
          </article>
          
          <!-- Bài viết liên quan -->
          <div class="related-posts mt-5">
            <h3>Bài viết liên quan</h3>
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
                      <a href="blog_detail.php?id=<?php echo $post['BlogId']; ?>" class="btn btn-info btn-sm">Đọc thêm</a>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          
          <div class="mt-4">
            <a href="blog.php" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left"></i> Trở về trang chính 
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
$commentsStmt->close();
$conn->close();

// Include footer
include "view/footer.php";
?>