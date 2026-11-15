# Lov Gårdbutik – lovgardbutik.dk

Mælk, kød og æg fra egne køer og høns, som går på græs.

WordPress + WooCommerce. Repoet indeholder kun `wp-content/themes`, `wp-content/plugins` og `wp-content/uploads`.
WordPress-kernen, `wp-config.php` og databasen er **ikke** i git.

## Arbejdsgang

1. Lav ændringer lokalt (XAMPP: `http://localhost/lovgardbutik`).
2. `git add -A && git commit -m "Beskrivelse"`
3. `git push` → GitHub Actions uploader ændringerne til one.com.

## Deploy-opsætning

Se kommentarerne øverst i `.github/workflows/deploy.yml` for hvilke secrets/variables der skal sættes på GitHub.

## Indhold (database)

Produkter, sider, ordrer og indstillinger ligger i databasen og synkroniseres **ikke** via git.
Første gang flyttes databasen manuelt (fx All-in-One WP Migration); derefter vedligeholdes indhold direkte på live-siden.
