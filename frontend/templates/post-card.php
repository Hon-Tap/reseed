<?php
/**
 * Template: Post Card
 *
 * This template is included by blog.php
 * It expects a $post variable to be available.
 */

// Set a fallback image
$cover_image = '/reseed/assets/images/Re1.jpg'; // Default fallback
if (!empty($post['cover_image'])) {
    $image_path = '/reseed/uploads/images/' . htmlspecialchars($post['cover_image']);
    $cover_image = $image_path;
}

// Generate the correct link (slug first, then ID)
$post_link = '/reseed/post.php?slug=' . htmlspecialchars($post['slug']);

// Format date
$date = 'Not Published';
if (!empty($post['published_at'])) {
    try {
        $date = (new DateTime($post['published_at']))->format('F j, Y');
    } catch (Exception $e) {
        $date = 'Invalid Date';
    }
}
?>

<article class="post-card" data-aos="fade-up" data-aos-delay="100">
  
  <!-- Card Image / Link -->
  <a href="<?php echo $post_link; ?>" class="card-image" aria-label="Read more about <?php echo htmlspecialchars($post['title']); ?>">
    <img src="<?php echo $cover_image; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
  </a>
  
  <!-- Card Content -->
  <div class="card-content">
    <h3>
      <a href="<?php echo $post_link; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
    </h3>
    
    <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
    
    <div class="card-footer">
      <span><?php echo $date; ?></span>
      <a href="<?php echo $post_link; ?>" class="read-more">Read More &rarr;</a>
    </div>
  </div>

</article>