<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fonction pour obtenir les détails d'un projet Blender
function getBlenderProjectDetails($filePath) {
    if (!file_exists($filePath)) {
        return null;
    }
    
    $fileName = pathinfo($filePath, PATHINFO_FILENAME);
    $fileSize = round(filesize($filePath) / 1024 / 1024, 2);
    $lastModified = date("Y-m-d H:i:s", filemtime($filePath));
    
    // Rechercher une image thumbnail
    $thumbnail = str_replace('.blend', '.jpg', $filePath);
    if (!file_exists($thumbnail)) {
        $thumbnail = str_replace('.blend', '.png', $filePath);
    }
    if (!file_exists($thumbnail)) {
        $thumbnail = "assets/img/default-blender.jpg";
    }
    
    // Rechercher des images supplémentaires
    $additionalImages = [];
    $imageDir = dirname($filePath);
    $imageFiles = glob($imageDir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    
    foreach ($imageFiles as $imageFile) {
        if ($imageFile !== $thumbnail) {
            $additionalImages[] = $imageFile;
        }
    }
    
    return [
        'name' => $fileName,
        'file' => $filePath,
        'thumbnail' => $thumbnail,
        'size' => $fileSize,
        'modified' => $lastModified,
        'additional_images' => $additionalImages
    ];
}

// Traitement du fichier demandé
$projectFile = isset($_GET['file']) ? urldecode($_GET['file']) : '';
$projectDetails = null;

if (!empty($projectFile) && file_exists($projectFile)) {
    $projectDetails = getBlenderProjectDetails($projectFile);
}

// Redirection si le projet n'existe pas
if (!$projectDetails) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($projectDetails['name']); ?> - Blender Project</title>
    
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    
    <style>
        .project-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        .project-info-card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
            margin-bottom: 2rem;
        }
        .project-image {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .back-button {
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="project-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="index.php#portfolio" class="btn btn-light back-button">
                        <i class="bi bi-arrow-left"></i> Back to Portfolio
                    </a>
                    <h1><?php echo htmlspecialchars($projectDetails['name']); ?></h1>
                    <p class="lead">Blender 3D Project</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?php echo $projectDetails['file']; ?>" class="btn btn-outline-light me-2" download>
                        <i class="bi bi-download"></i> Download .blend File
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Image -->
            <div class="col-lg-8 mb-4">
                <div class="card project-info-card">
                    <div class="card-body">
                        <img src="<?php echo $projectDetails['thumbnail']; ?>" 
                             alt="<?php echo htmlspecialchars($projectDetails['name']); ?>" 
                             class="img-fluid project-image w-100">
                    </div>
                </div>

                <!-- Additional Images -->
                <?php if (!empty($projectDetails['additional_images'])): ?>
                <div class="card project-info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Additional Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($projectDetails['additional_images'] as $image): ?>
                            <div class="col-md-4 mb-3">
                                <a href="<?php echo $image; ?>" class="glightbox" data-gallery="gallery">
                                    <img src="<?php echo $image; ?>" class="img-fluid rounded" alt="Additional view">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Project Details -->
            <div class="col-lg-4">
                <div class="card project-info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Project Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>File Name:</strong></td>
                                <td><?php echo htmlspecialchars($projectDetails['name']); ?>.blend</td>
                            </tr>
                            <tr>
                                <td><strong>File Size:</strong></td>
                                <td><?php echo $projectDetails['size']; ?> MB</td>
                            </tr>
                            <tr>
                                <td><strong>Last Modified:</strong></td>
                                <td><?php echo $projectDetails['modified']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Software:</strong></td>
                                <td>Blender 3D</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card project-info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo $projectDetails['file']; ?>" class="btn btn-primary" download>
                                <i class="bi bi-download"></i> Download Blender File
                            </a>
                            <a href="index.php#portfolio" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Portfolio
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card project-info-card">
                    <div class="card-header">
                        <h5 class="mb-0">Requirements</h5>
                    </div>
                    <div class="card-body">
                        <p>To view this project, you need:</p>
                        <ul>
                            <li>Blender 3.0 or higher</li>
                            <li>Recommended: 8GB RAM</li>
                            <li>Dedicated graphics card</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
    
    <script>
        // Initialize GLightbox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    </script>
</body>
</html>