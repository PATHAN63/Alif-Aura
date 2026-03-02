    </main>
    
    <footer class="main-footer">
        <div class="footer-content">
            <div class="footer-grid">
                <div class="footer-col footer-brand">
                    <a href="index.php">
                        <img src="images/logo.png" alt="Alif-Aura - Elegance in Every Layer" class="footer-logo">
                    </a>
                    <p class="footer-tagline"><?= SITE_TAGLINE ?></p>
                    <p>Luxury modest fashion for the discerning woman.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="shop.php?cat=abaya">Abaya Collection</a></li>
                        <li><a href="shop.php?cat=kids">Kids Collection</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Customer Care</h4>
                    <ul>
                        <li><a href="contact.php">FAQ</a></li>
                        <li><a href="contact.php">Shipping</a></li>
                        <li><a href="contact.php">Returns</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Newsletter</h4>
                    <form class="newsletter-form" action="newsletter.php" method="POST">
                        <input type="email" name="email" placeholder="Your email" required>
                        <button type="submit" class="btn-gold">Subscribe</button>
                    </form>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Alif-Aura. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/923001234567?text=Hi%20Alif-Aura!%20I'm%20interested%20in%20your%20collection" 
       class="whatsapp-float" target="_blank" rel="noopener" aria-label="Contact on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        <span>Chat with us</span>
    </a>

    <script src="js/main.js"></script>
    <?php if (isset($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
