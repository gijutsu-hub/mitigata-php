<?php
// ============================================================
//  VulnShop (PHP) — "Operation Black Friday" story-mode lab.
//  DELIBERATELY VULNERABLE. Design in lib.php; sinks are here.
// ============================================================
require __DIR__ . '/db.php';
require __DIR__ . '/coupon.php';
require __DIR__ . '/lib.php';
session_start();
$page = $_GET['page'] ?? 'home';
echo head_html($page);

function chapter_for($p){ global $CHAPTERS; foreach($CHAPTERS as $c) if($c['page']===$p) return $c; return null; }
$c = chapter_for($page);

switch ($page) {

// ---------------- Story landing ----------------
case 'home':
  echo '<section class=hero><div class=kicker>Mission Briefing</div>
    <h1>Operation&nbsp;Black&nbsp;Friday</h1>
    <p>ShadowMart runs the sketchiest storefront on the web. Your contract: work through the shop
    chapter by chapter and prove each flaw — from the staff door to a rogue loyalty engine.
    Six chapters, rising from <b>Easy</b> to <b>Insane</b>.</p></section>';
  echo '<div class=rail>'; foreach($CHAPTERS as $i=>$x) echo '<div class="step'.($i===0?' done':'').'"></div>'; echo '</div>';
  echo '<div class=grid>';
  foreach($CHAPTERS as $x){
    echo '<div class=chapter><div class=no>Chapter '.$x['no'].'</div>'
       .'<div class=meta>'.badge($x['level']).'</div>'
       .'<h3>'.$x['title'].'</h3><div class=desc>'.$x['desc'].'</div>'
       .'<a class=btn href="?page='.$x['page'].'">Enter chapter →</a></div>';
  }
  echo '</div>';
  echo '<div class=warn>⚠ This application is intentionally insecure. Run it only on an isolated host.</div>';
  break;

// ---------------- Ch1 · SQLi (EASY) ----------------
case 'login':
  echo panel_open($c);
  echo '<form class=vf method=post>
    <div><label>Username</label><input name=username placeholder="e.g. alice"></div>
    <div><label>Password</label><input name=password type=password placeholder="••••••••"></div>
    <div class=row><button class=btn>Sign in</button>
      <a class="btn ghost" href="?page=home">Abort</a></div></form>';
  if ($_SERVER['REQUEST_METHOD']==='POST'){
    $u=$_POST['username']??''; $p=$_POST['password']??'';
    $sql="SELECT * FROM users WHERE username = '$u' AND password = '$p'";
    try{ $row=db()->query($sql)->fetch(PDO::FETCH_ASSOC); }catch(Exception $e){ $row=null; $err=$e->getMessage(); }
    if(!empty($row)){
      $_SESSION['uid']=$row['id']; $_SESSION['role']=$row['role'];
      echo "<div class='out ok'>✔ Authenticated as <b>".htmlspecialchars($row['username'])
         ."</b> (role: ".htmlspecialchars($row['role']).")";
      if($row['role']==='admin') echo "\nADMIN SECRET: ".htmlspecialchars($row['secret']);
      echo "</div>";
    } else {
      echo "<div class=out>✘ Invalid credentials.\n<!-- debug query: ".htmlspecialchars($sql)." -->"
         .(isset($err)?"\nDB error: ".htmlspecialchars($err):"")."</div>";
    }
  }
  
  break;

// ---------------- Ch2 · Reflected XSS (EASY) ----------------
case 'search':
  echo panel_open($c);
  $q=$_GET['q']??'';
  echo "<form class=vf method=get><input type=hidden name=page value=search>
    <div><label>Search products</label><input name=q value='$q' placeholder='try: hoodie'></div>
    <button class=btn>Search</button></form>";
  if($q!=='') echo "<div class=out>Results for: $q\n(0 products matched — try harder.)</div>";
  
  break;

// ---------------- Ch3 · Command Injection (MEDIUM) ----------------
case 'ping':
  echo panel_open($c);
  $host=$_GET['host']??'';
  echo '<form class=vf method=get><input type=hidden name=page value=ping>
    <div><label>Host to reach</label><input name=host value="'.htmlspecialchars($host).'" placeholder="127.0.0.1"></div>
    <button class=btn>Run check</button></form>';
  if($host!==''){
    echo '<div class=out>';
    system("ping -c 1 " . $host);
    echo '</div>';
  }
  
  break;

// ---------------- Ch4 · IDOR (MEDIUM) ----------------
case 'note':
  echo panel_open($c);
  $id=$_GET['id']??($_SESSION['uid']??1);
  echo '<form class=vf method=get><input type=hidden name=page value=note>
    <div><label>Order note #</label><input name=id value="'.htmlspecialchars($id).'"></div>
    <button class=btn>Open note</button></form>';
  $stmt=db()->prepare("SELECT * FROM notes WHERE id = ?"); $stmt->execute([$id]);
  $n=$stmt->fetch(PDO::FETCH_ASSOC);
  echo $n ? "<div class=out>Note #".$n['id']." (owner ".$n['owner_id']."):\n".htmlspecialchars($n['body'])."</div>"
          : "<div class=out>No such note.</div>";
  
  break;

// ---------------- Ch5 · LFI (HARD) ----------------
case 'include':
  echo panel_open($c);
  $tpl=$_GET['tpl']??'welcome';
  echo '<form class=vf method=get><input type=hidden name=page value=include>
    <div><label>Help page</label><input name=tpl value="'.htmlspecialchars($tpl).'"></div>
    <button class=btn>Load page</button></form><div class=out>';
  @include(__DIR__ . "/pages/" . $tpl . ".php");
  echo '</div>';
  break;

// ---------------- Ch6 · Object Injection (INSANE) ----------------
case 'cart':
  echo panel_open($c);
  if(isset($_COOKIE['cart'])){
    $cart=unserialize(base64_decode($_COOKIE['cart']));
    echo "<div class=out>Cart restored:\n".htmlspecialchars(print_r($cart,true))."</div>";
  } else {
    $c0=['items'=>[],'total'=>0]; setcookie('cart',base64_encode(serialize($c0)));
    echo "<div class=out>New cart issued. Reload to see it restored from the cookie.</div>";
  }
  
  break;

default:
  echo '<div class=panel><h2>Unknown chapter</h2><a class=btn href="?page=home">Back to briefing</a></div>';
}
echo foot_html();
