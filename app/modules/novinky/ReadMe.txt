Modul Novinky pre LightPAGE
----------------------------

verzia: 1.00

Instalacia
----------
- Nakopirujte cely adresar novinky do adrasara web_root/app/modules
- Nakopirujte subor hotnews_block.php do adresara web_root/app/blocks
  - blok s novinkami mozete potom vlozit do stranky pomocou funckie <?php block::get('hotnews_block'); ?>
  - v nastaveniach noviniek (Menu administrácia -> Moduly -> Novinky) potom mozete nastavit pocet noviniek,
    ktore sa budu v bloku zobrazovat.

- V administracii kliknite na polozku Moduly/(Zoznam) a pri polozke Produkty stlacte tlacidlo Instalovat.
  Po naistalovani sa objavi sprava a tlacidlo sa zmeni na Odistalovat.
- Pri instalacii sa v administracii System -> Menu objavi nova polozka Novinky

URL pre vsetky novinky: <web_root>/novinky
URL pre jednotlive novinky: <web_root>/novinky/sprava/<id>