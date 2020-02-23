<?php
try {
  $dbh = new PDO('mysql:host=127.0.0.1;dbname=r6s', 'r6s', 'hojin7142');
} catch (Exception $e) {
  echo $e->getMessage();
}

/*
검색 아래에 버튼을 두고 최고mmr, 최고 승 등등 통계 버튼 두고 DESC 로 정렬
닉네임 검새하면 검색한것이 최 상단으로 이동
*/

//50개 플레이어 데이터 가져옴
//전체 players
$playersAll = $dbh->prepare('SELECT
  * ,
  RANK() OVER (order by mmr DESC) AS rank_mmr ,
  ROUND(PERCENT_RANK() OVER (ORDER BY mmr DESC),1)*10 AS pct_mmr,
  RANK() OVER (order by wins DESC) AS rank_wins ,
  ROUND(PERCENT_RANK() OVER (ORDER BY wins DESC),1)*10 AS pct_wins,
  RANK() OVER (order by losses DESC) AS rank_losses ,
  ROUND(PERCENT_RANK() OVER (ORDER BY losses DESC),1)*10 AS pct_losses,
  RANK() OVER (order by kills DESC) AS rank_kills ,
  ROUND(PERCENT_RANK() OVER (ORDER BY kills DESC),1)*10 AS pct_kills,
  RANK() OVER (order by deaths DESC) AS rank_deaths ,
  ROUND(PERCENT_RANK() OVER (ORDER BY deaths DESC),1)*10 AS pct_deaths,
  RANK() OVER (order by level DESC) AS rank_level ,
  ROUND(PERCENT_RANK() OVER (ORDER BY level DESC),1)*10 AS pct_level,
  RANK() OVER (order by deaths DESC) AS rank_deaths ,
  ROUND(PERCENT_RANK() OVER (ORDER BY xp DESC),1)*10 AS pct_xp
  FROM players ORDER BY id DESC LIMIT 50');
$playersAll->execute();
$list = $playersAll->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Francois+One&display=swap" rel="stylesheet">
    <title>r6s</title>
    <style media="screen">
      body {
        color: #f5f5f5;
        background-color: #1a1d22;
        font-family: 'Francois One';
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

      //player당 정보
      $playerDetail = $dbh->prepare("SELECT * FROM players WHERE profile_id = :profile_id");
      $playerDetail->bindParam(':profile_id',$profile_id);
      $playerDetail->execute();
      $playerDetalList = $playerDetail->fetchAll();
      foreach ($playerDetalList as $rows) {
        $mmrList[] = $rows['mmr'];
      }
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
        <!-- players stats-->
        <table style='text-align: center;'>
          <tr>
            <th>WINS</th>
            <th>LOSSES</th>
            <th>KILLS</th>
            <th>DEATHS</th>
            <th>MMR</th>
          </tr>
          <tr>
            <td><?php echo $wins.'<br>TOP '.$rows['rank_wins'].' '.$rows['pct_wins'].'%'?></td>
            <td><?php echo $losses.'<br>TOP '.$rows['rank_losses'].' '.$rows['pct_losses'].'%'?></td>
            <td><?php echo $kills.'<br>TOP '.$rows['rank_kills'].' '.$rows['pct_kills'].'%'?></td>
            <td><?php echo $deaths.'<br>TOP '.$rows['rank_deaths'].' '.$rows['pct_deaths'].'%'?></td>
            <td><?php echo $mmr.'<br>TOP '.$rows['rank_mmr'].' '.$rows['pct_mmr'].'%'?></td>
          </tr>
          <tr>
            <th>LEVEL</th>
            <th>XP</th>
          </tr>
          <tr>
            <td><?php echo $level.'<br>TOP '.$rows['rank_level'].' '.$rows['pct_level'].'%'?></td>
            <td><?php echo $xp.'<br>TOP '.$rows['rank_xp'].' '.$rows['pct_xp'].'%'?></td>
          </tr>
        </table>
      </div>

      <div class='rankInfo' style='display: flex; flex-direction: column; align-items: center; justify-content: center;'>
        <?php echo 'season '.$season.' | '.$region.' | '.$platform ?>
        <img class='rankInfo-image' style='width: 10em' src=<?php echo $rankInfoImage ?>>
        <?php echo $rankInfoName ?>
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
  echo ("<script>alert('Finish');location.href='/';</script>");
}

//전적 player api 에 요청
function getUser ($nickname, $dbh) {
  $getUser = file_get_contents("http://localhost:8000/getUser.php?name=".$nickname."&platform=uplay&appcode=r6s_api");
  //profile_id 추출
  $row = json_decode($getUser, true);
  $profile_id = array_keys($row['players'])[0];

  $InsertGetUser = $dbh->prepare("INSERT INTO players (profile_id, mmr, wins, losses, kills, deaths, level, xp, json) VALUES (:profile_id, :mmr, :wins, :losses, :kills, :deaths, :level, :xp, :json)");
  $InsertGetUser->bindParam(':json',$getUser);
  $InsertGetUser->bindParam(':profile_id',$profile_id);
  $InsertGetUser->bindParam(':mmr', $row['players'][$profile_id]['mmr']);
  $InsertGetUser->bindParam(':wins', $row['players'][$profile_id]['wins']);
  $InsertGetUser->bindParam(':losses', $row['players'][$profile_id]['losses']);
  $InsertGetUser->bindParam(':kills', $row['players'][$profile_id]['kills']);
  $InsertGetUser->bindParam(':deaths', $row['players'][$profile_id]['deaths']);
  $InsertGetUser->bindParam(':level', $row['players'][$profile_id]['level']);
  $InsertGetUser->bindParam(':xp', $row['players'][$profile_id]['xp']);
  $InsertGetUser->execute();
  return $getUser;
}
 ?>
