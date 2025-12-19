<?php
/**
 * Template: Project Card
 *
 * This template is included by projects.php
 * It expects a $project variable to be available.
 */

// Set a fallback image
$cover_image = '/reseed/assets/images/Re2.jpg'; // Default fallback
if (!empty($project['cover_image'])) {
    $image_path = '/reseed/uploads/images/' . htmlspecialchars($project['cover_image']);
    // You could add a file_exists check here if you want to be extra robust
    $cover_image = $image_path;
}

// Generate the correct link (slug first, then ID)
$project_link = '/reseed/post.php?project_slug=' . htmlspecialchars($project['slug']);
?>

<article class="project-card" data-aos="fade-up" data-aos-delay="100">
  
  <!-- Card Image / Link -->
  <a href="<?php echo $project_link; ?>" class="card-image" aria-label="Read more about <?php echo htmlspecialchars($project['title']); ?>">
    <img src="<?php echo $cover_image; ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
  </a>
  
  <!-- Card Content -->
  <div class="card-content">
    <h3>
      <a href="<?php echo $project_link; ?>"><?php echo htmlspecialchars($project['title']); ?></a>
    </h3>
    
    <p><?php echo htmlspecialchars($project['summary']); ?></p>
    
    <div class="card-footer">
      <span class="status-badge"><?php echo htmlspecialchars($project['status'] ?? 'Ongoing'); ?></span>
      <a href="<?php echo $project_link; ?>" class="read-more">View Project &rarr;</a>
    </div>
  </div>

</article>