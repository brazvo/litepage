Modul Produkty pre LightPAGE
----------------------------

verzia: 1.50

Instalacia
----------
- Nakopirujte cely adresar produkty do adrasara web_root/app/modules
- Nakopirujte subor products_menu.php do adresara web_root/app/blocks
  - blok s polozkami menu mozete potom vlozit do stranky pomocou funckie <?php block::get('products_menu'); ?>
    alebo mozete zavolat funkciu <?php echo block::getMenu('produkty'); ?>

- V administracii kliknite na polozku Moduly/(Zoznam) a pri polozke Produkty stlacte tlacidlo Instalovat.
  Po naistalovani sa objavi sprava a tlacidlo sa zmeni na Odistalovat.
- Pri instalacii sa v administracii System -> Menu objavi nova polozka Produkty
- jednotlive polozky (kategorie produktov) mozete pridavat tak, ze kliknete na ikonu polozky a potom na Pridat polozku.

Novinky vo verzii 1.50
- Pridana moznost volitelneho zoradenia produktov v kategoriach budto podla nazvu produktu alebo podla priority, ktora je manualne nastavitelna.
Ak v nastaveniach modulu Produkty zvolite moznost zoradenia podla priority, tak sa v editacnom formulare pre upravu produktu
zobrazi zoznam produktov v kategorii a mate moznost presunut produkt pred zvolenu poziciu.