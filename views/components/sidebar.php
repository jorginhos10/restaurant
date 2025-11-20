<?php
// Cargar el sistema de permisos
require_once 'config/permissions.php';
$permissions = new Permissions();
$menuItems = $permissions->getMenuItems();

// Determinar la ruta actual para resaltar el item activo
$currentUrl = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$currentSection = explode('/', $currentUrl)[0];
?>

<aside class="sidebar">
    <nav class="sidebar-nav">
        <ul>
            <?php foreach ($menuItems as $key => $item): ?>
            <li>
                <a href="<?php echo $item['url']; ?>" 
                   class="nav-link <?php echo $currentSection === $key ? 'active' : ''; ?>"
                   data-tooltip="<?php echo $item['nombre']; ?>">
                    <?php if (isset($item['url_icon']) && !empty($item['url_icon'])): ?>
                        <img src="<?php echo $item['url_icon']; ?>" 
                             alt="<?php echo $item['nombre']; ?>" 
                             class="nav-icon-img"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-block';">
                        <span class="nav-icon" style="display: none;"><?php echo $item['icono']; ?></span>
                    <?php else: ?>
                        <span class="nav-icon"><?php echo $item['icono']; ?></span>
                    <?php endif; ?>
                    <span class="nav-text"><?php echo $item['nombre']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<main class="main-content">