<?php
try {
  $dbh = new PDO('mysql:host=127.0.0.1;dbname=r6s', 'r6s', 'hojin7142');
} catch (Exception $e) {
  echo $e->getMessage();
}

//50개 오버뷰 데이터 가져옴
$stmt = $dbh->prepare('SELECT id, json FROM overview LIMIT 50');
$stmt->execute();
$list = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <style media="screen">
      .container {
        padding: 1em;
        margin: 0.5em;
        background-color: rgba(41,44,51,.25);
        border-radius: 15px;
        opacity: 0.85;
        display: flex;
        flex-direction: row;
        justify-content: space-between;

      }
      /* container 애니메이션 */
      .container {
        animation-name: containerAppend;
        animation-duration: 1.5s;
        /* animation-iteration-count: infinite; */
      }
      @keyframes containerAppend {
        from { margin-bottom: 80%; opacity: 0.3; }
        to { margin-bottom: 0%; opacity: 1; }
      }
    </style>
  </head>
  <body>
    <?php
    foreach ($list as $rows) {
      echo "<div class='container'>";
      $row = json_decode($rows['json'], true);
      $profile_id = array_keys($row['players'])[0];
      $overview = $row['players'][$profile_id];
      $nickname = $row['players'][$profile_id]['nickname'];
      $level = $row['players'][$profile_id]['level'];
      $mmr = $row['players'][$profile_id]['mmr'];

      $rankInfoImage = $overview['rankInfo']['image'];
      $rankInfoName = $overview['rankInfo']['name'];
      echo "<div class='overview'>";
      echo "<H1>".$nickname."</H1>";
      echo "<H1>level ".$level."</H1>";
      echo "<H1>mmr ".$mmr."</H1>";
      echo "</div>";
      echo "<div class='rankInfo'>";
      echo "<img class='rankInfo-image'style='width: 200px' src='".$rankInfoImage."'>";
      echo "<h2>".$rankInfoName."</h2>";
      echo "</div>";
      echo "</div>";
    }

    // getUser('LE16_', $dbh);
    ?>
  </body>
</html>


<?php
//전적 오버뷰 api 에 요청
function getUser ($nickname, $dbh) {
  $getUser = file_get_contents("http://localhost:8000/getUser.php?name=".$nickname."&platform=uplay&appcode=r6s_api");
  $InsertGetUser = $dbh->prepare("INSERT INTO overview (json) VALUES (:json)");
  $InsertGetUser->bindParam(':json',$getUser);
  $InsertGetUser->execute();
}
 ?>
