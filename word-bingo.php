<?php

$bingo_size = trim(fgets(STDIN));
$bingo_card = initBingoCard($bingo_size);
$selected_list = loadSelectedList();
$bingo_card_checked = fillSelectedBingoCell($bingo_size, $bingo_card, $selected_list);
$bingo_results = checkBingoEvaluations($bingo_card_checked);
$result = evaluateBingo($bingo_results);
echo $result;
echo "\n";

// ★ビンゴカードの単語が選ばれているかの判定方法
// 多次元配列でビンゴカードにある単語をkey、0をvalueで設定
// カード内の単語が選ばれればvalueを1に変更
// ex)2×2のカードの場合
// 最初のカード設定($bingo_card)
// [0] => [apple=>0 , grape=>0]
// [1] => [php=>0 , ruby=>0]
// もしappleが選ばれた場合、下記に変更($bingo_card_checked)
// [0] => [apple=>1 , grape=>0]
// [1] => [php=>0 , ruby=>0]


/**
 * ビンゴカードのwordを配列にいれる
 * ビンゴカードの各単語にまだ選ばれていない単語 = 0を設定
 * 
 * @param int $bingo_size ビンゴのカードサイズ
 * @return array ビンゴカードの単語、0
 */
function initBingoCard(int $bingo_size): array
{
  $bingo_card = [];
  for ($i = 0; $i < $bingo_size; $i++) {
    $word_list = explode(" ", trim(fgets(STDIN)));
    for ($j = 0; $j < $bingo_size; $j++) {
      $bingo_card[$i][$word_list[$j]] = 0;
    }
  }
  return $bingo_card;
}


/**
 * 選ばれたワードを配列にいれる
 * 
 * @return array ビンゴで選ばれた単語一覧
 */
function loadSelectedList(): array
{
  $num_selected = trim(fgets(STDIN));
  $selected_list = [];
  for ($i = 0; $i < $num_selected; $i++) {
    $selected_list[] = trim(fgets(STDIN));
  }
  return $selected_list;
}

/**
 * 選ばれた単語がカードの単語と一致すれば各単語のvalueを1に変更
 * 
 * @param int $bingo_size カードのサイズ  
 * @param array $bingo_card ビンゴカードの単語一覧
 * @param array $selected_list 選ばれた単語の配列
 * @return array カードの単語、登場した単語の配列のvalueは1,登場していない単語のvalueは0
 */
function fillSelectedBingoCell(int $bingo_size, array $bingo_card, array $selected_list): array
{
  for ($i = 0; $i < $bingo_size; $i++) {
    for ($j = 0; $j < count($selected_list); $j++) {
      if (array_key_exists($selected_list[$j], $bingo_card[$i])) {
        $bingo_card[$i][$selected_list[$j]] = 1;
      }
    }
  }
  return $bingo_card;
}

/**
 * 横列にビンゴがあるかの判定
 * 
 * @param array $bingo_card_checked 選ばれた単語判定後のビンゴカード
 * @return bool true: ビンゴになった横列あった false: ビンゴになった横列なかった
 */
function judgeRowBingo(array $bingo_card_checked): bool
{
  foreach ($bingo_card_checked as $bingo_row) {
    $selected_row = array_count_values($bingo_row);
    if ($selected_row[1] == count($bingo_card_checked)) return true;
  }
  return false;
}

/**
 * 斜め列がビンゴしているかの判定
 * 斜めに値するカードのマスの単語が選ばれている場合
 * 判断基準となる$diagonal_countへ1づつ足していく
 * $diagonal_countの値がビンゴカードのサイズと合えばbingo
 * 
 * @param array $bingo_card_checked 選ばれた単語判定後のビンゴカード
 * @return bool true: 斜め列がビンゴになった false: 斜め列がビンゴにならなかった
 */
function judgeDiagonalBingo(array $bingo_card_checked): bool
{
  $diagonal_count = 0;
  foreach ($bingo_card_checked as $key1 => $value1) {
    $search_diagonal = array_slice($value1, $key1, 1);
    $diagonal_value = array_values($search_diagonal);
    if ($diagonal_value[0] == 1) {
      $diagonal_count++;
    }
  }
  return ($diagonal_count == count($bingo_card_checked));
}

/**
 * 斜め列がビンゴしているかの判定
 * judgeDiagonalBingoの反対側の斜め列を判定
 * 判断基準となる$diagonal_count_reverseへ1づつ足していく
 * $diagonal_count_reverseの値がビンゴカードのサイズと合えばbingo
 *  
 * @param array $bingo_card_checked 選ばれた単語判定後のビンゴカード
 * @return bool true: 斜め列がビンゴになった false: 斜め列がビンゴにならなかった
 */
function judgeDiagonalBingoReverse(array $bingo_card_checked): bool
{
  $diagonal_count_reverse = 0;
  foreach ($bingo_card_checked as $key2 => $value2) {
    $value_reverse = array_reverse($value2);
    $search_diagonal_reverse = array_slice($value_reverse, $key2, 1);
    $diagonal_value_reverse = array_values($search_diagonal_reverse);
    if ($diagonal_value_reverse[0] == 1) {
      $diagonal_count_reverse++;
    }
  }
  return ($diagonal_count_reverse == count($bingo_card_checked));
}

/**
 * 縦列にビンゴがあるかの判定
 * 縦のライン毎に配列を作り、0か1かの値を足し算していく
 * それぞれの列の足し算結果がビンゴサイズとあっていればbingo判定
 *  
 * @param array $bingo_card_checked 選ばれた単語判定後のビンゴカード
 * @return bool true: 縦列がビンゴになった false: 縦列がビンゴにならなかった
 */
function judgeCol(array $bingo_card_checked): bool
{
  $col = [];
  for ($i = 0; $i < count($bingo_card_checked); $i++) {
    $col[$i] = 0;
  }
  foreach ($bingo_card_checked as $key3 => $value3) {
    for ($i = 0; $i < count($bingo_card_checked); $i++) {
      $search_col = array_slice($value3, $i, 1);
      $col_line = array_values($search_col);
      $col[$i] += $col_line[0];
    }
  }
  for ($i = 0; $i < count($bingo_card_checked); $i++) {
    if ($col[$i] == count($bingo_card_checked)) return true;
  }
  return false;
}

/**
 * 縦横斜めのビンゴ有無を配列にまとめる
 * 
 * @param array $bingo_card_checked 選ばれた単語の判定後のビンゴカード
 * @return array 縦横斜めのビンゴ有無結果の配列 
 */
function checkBingoEvaluations(array $bingo_card_checked): array
{
  $bingo_evaluations = [];
  $bingo_evaluations[] = judgeRowBingo($bingo_card_checked);
  $bingo_evaluations[] = judgeDiagonalBingo($bingo_card_checked);
  $bingo_evaluations[] = judgeDiagonalBingoReverse($bingo_card_checked);
  $bingo_evaluations[] = judgeCol($bingo_card_checked);
  return $bingo_evaluations;
}

/**
 * ビンゴがあったかどうか最終判定
 *
 * @param array $bingo_results 全列のビンゴ有無結果
 * @return string いずれかの列にビンゴがあればyes ビンゴなければnoを返す
 */
function evaluateBingo(array $bingo_results): string
{
  return in_array(true, $bingo_results, true)
    ? "yes"
    : "no";
}
