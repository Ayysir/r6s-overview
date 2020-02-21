<?php
try {
  $dbh = new PDO('mysql:host=127.0.0.1;dbname=r6s', 'r6s', 'hojin7142');
} catch (Exception $e) {
  echo $e->getMessage();
}

//50개 플레이어 데이터 가져옴
//profile_id를 기준으로 중복없이 출력
$stmt = $dbh->prepare('SELECT DISTINCT profile_id, json FROM players ORDER BY id DESC LIMIT 50');
$stmt->execute();
$list = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <title>r6s</title>
    <style media="screen">
      body {
        color: #f5f5f5;
        background-color: #1a1d22;
      }
      .container {
        padding: 0.5em;
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

      .search {
       height: 3em;
       background-color: #292c33;
       border-radius: 15px;
       opacity: 0.65;
       border: none;
       color: #f5f5f5;
      }
      .search input {
       background-color: #292c33;
       width: 90%;
       height: 80%;
       margin : 0.3em;
       border-radius: 15px;
       opacity: 0.65;
       border: none;
       color: #f5f5f5;
       font-size: 1em;
      }
    </style>
  </head>
  <body>
    <form class="search nickname">
       <input type="text" name="nickname" value="" placeholder="Search...">
    </form>
    <?php
    // 데이터를 조합
    foreach ($list as $rows) {
      $row = json_decode($rows['json'], true);

      $profile_id = array_keys($row['players'])[0];
      $overview = $row['players'][$profile_id];
      $xp = $row['players'][$profile_id]['xp'];
      $lootbox_probability = $row['players'][$profile_id]['lootbox_probability'];
      $level = $row['players'][$profile_id]['level'];
      $max_mmr = $row['players'][$profile_id]['max_mmr'];
      $skill_mean = $row['players'][$profile_id]['skill_mean'];
      $deaths = $row['players'][$profile_id]['deaths'];
      $next_rank_mmr = $row['players'][$profile_id]['next_rank_mmr'];
      $rank = $row['players'][$profile_id]['rank'];
      $max_rank = $row['players'][$profile_id]['max_rank'];
      $board_id = $row['players'][$profile_id]['board_id'];
      $skill_stdev = $row['players'][$profile_id]['skill_stdev'];
      $kills = $row['players'][$profile_id]['kills'];
      $last_match_skill_stdev_change = $row['players'][$profile_id]['last_match_skill_stdev_change'];
      $update_time = $row['players'][$profile_id]['update_time'];
      $last_match_mmr_change = $row['players'][$profile_id]['last_match_mmr_change'];
      $abandons = $row['players'][$profile_id]['abandons'];
      $season = $row['players'][$profile_id]['season'];
      $top_rank_position = $row['players'][$profile_id]['top_rank_position'];
      $last_match_skill_mean_change = $row['players'][$profile_id]['last_match_skill_mean_change'];
      $mmr = $row['players'][$profile_id]['mmr'];
      $previous_rank_mmr = $row['players'][$profile_id]['previous_rank_mmr'];
      $last_match_result = $row['players'][$profile_id]['last_match_result'];
      $wins = $row['players'][$profile_id]['wins'];
      $region = $row['players'][$profile_id]['region'];
      $losses = $row['players'][$profile_id]['losses'];
      $nickname = $row['players'][$profile_id]['nickname'];
      $platform = $row['players'][$profile_id]['platform'];

      $rankInfoImage = $overview['rankInfo']['image'];
      $rankInfoName = $overview['rankInfo']['name'];
      $error = $overview['error']['message'];
    ?>
    <div class='container'>
      <!-- // 에러 수신시 에러 출력 -->
      <?php if (!empty($error)) {
        $nickname = $error."\n".$profile_id;
        $rankInfoImage = "https://r6tab.com/images/pngranks/0.png?x=3";
        $rankInfoName = "Unranked";
      }
      ?>
      <div class='overview'>
        <H1 class='nickname'><?php echo $nickname?></H1>
        <table style='text-align: center;'>
          <tr>
            <th>Wins</th>
            <th>Losses</th>
            <th>Kills</th>
            <th>Deaths</th>
          </tr>
          <tr>
            <td><?php echo $wins?></td>
            <td><?php echo $losses?></td>
            <td><?php echo $kills?></td>
            <td><?php echo $deaths?></td>
          </tr>
          <tr>
            <th>mmr</th>
            <th>max mmr</th>
            <th>pre mmr<th>
          </tr>
          <tr>
            <td><?php echo $mmr?></td>
            <td><?php echo $max_mmr?></td>
            <td><?php echo $previous_rank_mmr?></td>
          </tr>
        </table>
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
  //javascript로 오버레이에 찾는중 표시
  getUser($_GET['nickname'], $dbh);
  echo ("<script>alert('Finish');location.href='/';</script>") ;
}

//전적 player api 에 요청
function getUser ($nickname, $dbh) {
  $getUser = file_get_contents("http://localhost:8000/getUser.php?name=".$nickname."&platform=uplay&appcode=r6s_api");
  //profile_id 추출
  $row = json_decode($getUser, true);
  $profile_id = array_keys($row['players'])[0];

  $InsertGetUser = $dbh->prepare("INSERT INTO players (profile_id, json) VALUES (:profile_id, :json)");
  $InsertGetUser->bindParam(':json',$getUser);
  $InsertGetUser->bindParam(':profile_id',$profile_id);
  $InsertGetUser->execute();
  return $getUser;
}
 ?>
