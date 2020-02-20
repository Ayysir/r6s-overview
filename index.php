<?php
try {
  $dbh = new PDO('mysql:host=127.0.0.1;dbname=r6s', 'r6s', 'hojin7142');
} catch (Exception $e) {
  echo $e->getMessage();
}

//50개 오버뷰 데이터 가져옴
$stmt = $dbh->prepare('SELECT id, json FROM overview ORDER BY id DESC LIMIT 50');
$stmt->execute();
$list = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
    <style media="screen">
      body {
        color: #f5f5f5;
        background-color: #1a1d22;
      }
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
      .container .nickname {
        color: #fbc900;
      }
    </style>
  </head>
  <body>
    <?php
    // 데이터를 조합
    foreach ($list as $rows) {
      $row = json_decode($rows['json'], true);

      $profile_id = array_keys($row['players'])[0];
      $overview = $row['players'][$profile_id];
      $nickname = $row['players'][$profile_id]['nickname'];
      $level = $row['players'][$profile_id]['level'];
      $mmr = $row['players'][$profile_id]['mmr'];

      $rankInfoImage = $overview['rankInfo']['image'];
      $rankInfoName = $overview['rankInfo']['name'];
      $error = $overview['error']['message'];
    ?>
    <div class='container'>
      <!-- // 에러 수신시 에러 출력 -->
      <?php if (!empty($error)) {
        echo $error;
      }
      ?>
      <div class='overview'>
        <H1 class='nickname'><?php echo $nickname ?></H1>
        <H1><?php echo $level ?></H1>
        <H1><?php echo $mmr ?></H1>
      </div>

      <div class='rankInfo'>
        <img class='rankInfo-image'style='width: 200px' src=<?php echo $rankInfoImage ?>>
        <h2 style='text-align:center;'><?php echo $rankInfoName ?></h2>
      </div>
    </div>
  <?php }
  // foreach end
  ?>
  </body>
</html>


<?php
// get 방식으로 요청시 해당 닉네임으로 정보 찾기
if ($_GET['nickname'] != null) {
  getUser($_GET['nickname'], $dbh);
}

//전적 오버뷰 api 에 요청
function getUser ($nickname, $dbh) {
  $getUser = file_get_contents("http://localhost:8000/getUser.php?name=".$nickname."&platform=uplay&appcode=r6s_api");
  $InsertGetUser = $dbh->prepare("INSERT INTO overview (json) VALUES (:json)");
  $InsertGetUser->bindParam(':json',$getUser);
  $InsertGetUser->execute();
}
 ?>
