<?php

$bingo_size = trim(fgets(STDIN));
$bingo_card = getBingoCard($bingo_size, $file);
$selected_list = getSelectedList();
$bingo_card_after = getCardSelected($bingo_size, $bingo_card, $selected_list);
$row_judge = getRowJudge($bingo_size,$bingo_card_after);
$diagonal_judge1 = getDiagonalJudge1($bingo_size,$bingo_card_after);
$diagonal_judge2 = getDiagonalJudge2($bingo_size, $bingo_card_after);
$col_judge = getColJudge($bingo_size, $bingo_card_after);
$final_judge = getFinalJudge($row_judge, $diagonal_judge1, $diagonal_judge2, $col_judge);
echo in_array("bingo", $final_judge) ? "yes" : "no";




//ビンゴカードのwordを配列に
//ビンゴカードの各単語にまだ選ばれていない = 0を設定
//@param $bingo_size
//@return $bingo_card
// function getBingoCard($bingo_size)
function getBingoCard($bingo_size)
{
  $bingo_card = array();
  for ($i = 0; $i < $bingo_size; $i++) {
    $word_list = trim(fgets(STDIN));
    $list_explode = explode(" ", $word_list);
    for ($j = 0; $j < $bingo_size; $j++) {
      $bingo_card[$i][$list_explode[$j]] = 0;
    }
  }
  return $bingo_card;
}

// 選ばれたワードを配列に
function getSelectedList()
{
  $num_selected = trim(fgets(STDIN));
  $selected_list = array();
  for ($k = 0; $k < $num_selected; $k++) {
    $selected_list[] = trim(fgets(STDIN));
  }
  return $selected_list;
}


//選ばれた単語がカードの単語と一致すれば各単語の連想配列valueを1に変更
function getCardSelected($bingo_size, $bingo_card, $selected_list)
{
  for ($l = 0; $l < $bingo_size; $l++) {
    for ($m = 0; $m < count($selected_list); $m++) {
      if (array_key_exists($selected_list[$m], $bingo_card[$l])) {
        $bingo_card[$l][$selected_list[$m]] = 1;
      }
    }
  }
  return $bingo_card;
}

//ビンゴ横列の判定
function getRowJudge($bingo_size, $bingo_card_after)
{
  $row_judge = array();
  for ($n = 0; $n < $bingo_size; $n++) {
    $selected_row = array_count_values($bingo_card_after[$n]);
    if ($selected_row[1] == $bingo_size) $row_judge = "bingo";
  }
  return $row_judge;
}

//斜めに値するカードのマスが既に選ばれている場合
//判断基準となる$d_judge1へ1づつ足していく
// $judge1の値がビンゴカードのサイズと合えばbingo
function getDiagonalJudge1($bingo_size, $bingo_card_after)
{
    $diagonal_count1 = 0;
    foreach ($bingo_card_after as $key1 => $value1) {
      $search_diagonal1 = array_slice($value1, $key1, 1);
      $d_judge1 = array_values($search_diagonal1);
      if ($d_judge1[0] == 1) {
        $diagonal_count1 = $diagonal_count1 + 1;
      }
    }
    if ($diagonal_count1 == $bingo_size) {
      $diagonal_judge1 = "bingo";
    }
    return $diagonal_judge1;
}

// getDiagonalJudge1の反対側の斜め判定
function getDiagonalJudge2($bingo_size, $bingo_card_after)
{
  $diagonal_count2 = 0;
  foreach ($bingo_card_after as $key2 => $value2) {
    $value_reverse = array_reverse($value2);
    $search_diagonal2 = array_slice($value_reverse, $key2, 1);
    $d_judge2 = array_values($search_diagonal2);
    if ($d_judge2[0] == 1) {
      $diagonal_count2 = $diagonal_count2 + 1;
    }
  }
  if ($diagonal_count2 == $bingo_size) {
    $diagonal_judge2 = "bingo";
  }
  return $diagonal_judge2;
}  

//縦の判定
//縦のライン毎に配列を作り値を足し算していく
//それぞれの値がビンゴサイズとあっていればbingo判定
function getColJudge($bingo_size, $bingo_card_after)
{
  $col = array();
  foreach ($bingo_card_after as $key3 => $value3) {
    for ($o = 0; $o < $bingo_size; $o++) {
      $search_col = array_slice($value3, $o, 1);
      $col_line = array_values($search_col);
      $col[$o] += $col_line[0];
    }
  }
  for ($p = 0; $p < $bingo_size; $p++) {
    if ($col[$p] == $bingo_size) $col_judge = "bingo";
  }
  return $col_judge;
}

//横・縦・斜めのJudge結果を結合
function getFinalJudge($row_judge, $diagonal_judge1, $diagonal_judge2, $col_judge)
{
  $final_judge = [$row_judge, $diagonal_judge1, $diagonal_judge2, $col_judge];
  return $final_judge;
}