<?php
$pageTitle = 'Blog';
require_once 'includes/header.php';
?>

<section class="section fade-in">
    <h1 class="section-title">Fashion Tips & Trends</h1>
    <div class="blog-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:2rem;max-width:1200px;margin:0 auto">
        <article class="blog-card" style="background:#1a1a1a;border:1px solid rgba(212,175,55,0.2);overflow:hidden">
            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=600&q=80" alt="Modest Fashion" style="width:100%;height:200px;object-fit:cover">
            <div style="padding:1.5rem">
                <h3 style="color:#D4AF37;font-family:var(--font-display);margin-bottom:0.5rem">Styling Your Abaya for Different Occasions</h3>
                <p style="color:#8a8a8a;font-size:0.9rem">Discover how to elevate your modest look for weddings, casual outings, and formal events.</p>
                <a href="#" class="btn-outline" style="margin-top:1rem;display:inline-block;padding:0.5rem 1rem;font-size:0.85rem">Read More</a>
            </div>
        </article>
        <article class="blog-card" style="background:#1a1a1a;border:1px solid rgba(212,175,55,0.2);overflow:hidden">
            <img src="https://images.unsplash.com/photo-1503919545889-aef636e10ad4?w=600&q=80" alt="Kids Fashion" style="width:100%;height:200px;object-fit:cover">
            <div style="padding:1.5rem">
                <h3 style="color:#D4AF37;font-family:var(--font-display);margin-bottom:0.5rem">Modest Fashion for Little Ones</h3>
                <p style="color:#8a8a8a;font-size:0.9rem">Tips for dressing your children in comfortable, stylish modest wear.</p>
                <a href="#" class="btn-outline" style="margin-top:1rem;display:inline-block;padding:0.5rem 1rem;font-size:0.85rem">Read More</a>
            </div>
        </article>
        <article class="blog-card" style="background:#1a1a1a;border:1px solid rgba(212,175,55,0.2);overflow:hidden">
            <img src="https://images.unsplash.com/photo-1558171813-1e38e9d4a5e1?w=600&q=80" alt="Care Tips" style="width:100%;height:200px;object-fit:cover">
            <div style="padding:1.5rem">
                <h3 style="color:#D4AF37;font-family:var(--font-display);margin-bottom:0.5rem">Caring for Your Luxury Abayas</h3>
                <p style="color:#8a8a8a;font-size:0.9rem">Best practices for washing, storing, and maintaining your premium modest wear.</p>
                <a href="#" class="btn-outline" style="margin-top:1rem;display:inline-block;padding:0.5rem 1rem;font-size:0.85rem">Read More</a>
            </div>
        </article>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
