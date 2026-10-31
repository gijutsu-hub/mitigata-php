<?php
// Shared layout + story metadata for VulnShop (PHP).  Design only; sinks live in index.php.
$CHAPTERS = [
  ['no'=>1,'page'=>'login',  'title'=>'The Staff Door',      'level'=>'easy',
   'owasp'=>'A03 · SQL Injection',
   'desc'=>"ShadowMart's employee portal guards everything. Slip past the login without a password.",
   'story'=>"You found the staff login of ShadowMart. The query that checks your password was... trusting."],
  ['no'=>2,'page'=>'search', 'title'=>'Poisoned Shelves',    'level'=>'easy',
   'owasp'=>'A03 · Reflected XSS',
   'desc'=>"The product search echoes whatever shoppers type. Make it echo your script.",
   'story'=>"Every search term is painted straight back onto the page. What if the term were code?"],
  ['no'=>3,'page'=>'ping',   'title'=>"Ops' Backdoor",       'level'=>'medium',
   'owasp'=>'A03 · OS Command Injection',
   'desc'=>"The internal network tool pings hosts. The shell underneath will run more than pings.",
   'story'=>"Ops left a diagnostics box online. It hands your input straight to a shell."],
  ['no'=>4,'page'=>'note',   'title'=>'Other People\'s Orders','level'=>'medium',
   'owasp'=>'A01 · IDOR',
   'desc'=>"Order notes load by number. Nothing checks whose order it is.",
   'story'=>"Your receipt is note #1. The manager's secrets sit at a higher number."],
  ['no'=>5,'page'=>'include','title'=>'The Help Center',     'level'=>'hard',
   'owasp'=>'A03/A05 · File Inclusion',
   'desc'=>"Help pages are loaded by name. Bend the path to read source — or worse.",
   'story'=>"The help center builds a file path from the page you ask for. Ask for the wrong file."],
  ['no'=>6,'page'=>'cart',   'title'=>'Loyalty Gone Rogue',  'level'=>'insane',
   'owasp'=>'A08 · PHP Object Injection',
   'desc'=>"Your cart rides in a cookie as a serialized object. Forge one with teeth.",
   'story'=>"The cart cookie is a trusted serialized object. The Coupon class bites on destruct."],
];
function badge($l){ $m=['easy'=>'EASY','medium'=>'MEDIUM','hard'=>'HARD','insane'=>'INSANE'];
  return "<span class='badge b-$l'>".$m[$l]."</span>"; }
function head_html($active=''){
  global $CHAPTERS; ob_start(); ?>
<!doctype html><html lang=en><head><meta charset=utf-8>
<meta name=viewport content="width=device-width,initial-scale=1">
<title>VulnShop · Operation Black Friday</title>
<link rel=stylesheet href="style.css"></head><body>
<header class=topbar><div class="wrap">
  <div class=brand><span class=dot></span> VulnShop <small>· PHP target</small></div>
  <nav>
    <a href="?page=home" class="<?= $active==='home'?'on':'' ?>">Briefing</a>
    <?php foreach($CHAPTERS as $c): ?>
      <a href="?page=<?= $c['page'] ?>" class="<?= $active===$c['page']?'on':'' ?>"><?= $c['no'] ?></a>
    <?php endforeach; ?>
  </nav>
</div></header><div class=wrap>
<?php return ob_get_clean(); }
function foot_html(){ return '</div><footer><div class=wrap>VulnShop — deliberately vulnerable training target. '
  .'Authorized use only · fixes in <code>solutions/php-solutions.md</code></div></footer></body></html>'; }
function panel_open($c){
  return "<div class=panel><div class=meta style='display:flex;gap:10px;align-items:center;margin-bottom:6px'>"
    ."<span class=tag>Chapter ".$c['no']."</span>".badge($c['level'])
    ."</div>"
    ."<h2>".$c['title']."</h2><p class=story>".$c['story']."</p>"; }
