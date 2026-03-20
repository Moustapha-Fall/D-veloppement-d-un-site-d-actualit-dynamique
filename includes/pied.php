
</main><!-- /.site-main -->

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-brand">
            <span class="logo-icon">📰</span>
            <strong>ActuSénégal</strong>
        </div>
        <p class="footer-copy">&copy; <?= date('Y') ?> ActuSénégal — ESP Dakar, Projet Backend PHP/MySQL</p>
        <p class="footer-tech">Développé avec PHP · MySQL · JavaScript</p>
    </div>
</footer>

<script src="<?= $base_url ?? '' ?>js/validation.js"></script>
<script>
// Menu burger mobile
const burger = document.getElementById('menuBurger');
const nav    = document.querySelector('.main-nav');
if (burger && nav) {
    burger.addEventListener('click', () => {
        nav.classList.toggle('open');
        burger.classList.toggle('active');
    });
}
// Dropdowns nav
document.querySelectorAll('.nav-dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', e => {
        e.preventDefault();
        const parent = toggle.closest('.nav-dropdown');
        parent.classList.toggle('active');
    });
});
</script>
</body>
</html>
