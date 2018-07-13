</main>

<footer class="footer">
    <div class="container">
        <span class="text-muted">&copy; NT-Core</span>
    </div>
</footer>

<?php
$html->addScriptString('<!-- Icons -->
<script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>', 'footer', 10);

require_once(__DIR__.DIRECTORY_SEPARATOR.'footer.sub.php');
?>