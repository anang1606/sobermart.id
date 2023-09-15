<?php

namespace Botble\Accounting\Helpers;

use Botble\Accounting\Models\LabaRugi as ModelsLabaRugi;
use DB;

class LabaRugi
{
    private $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function htmlList()
    {
        // return $this->itemWithChildren($this->items);
        // return $this->itemArray();
        return $this->htmlFromArray($this->itemArray());
    }

    private function itemArray()
    {
        $result = array();
        $search_parent = ModelsLabaRugi::where('parent', $this->items->id)->get();
        foreach ($search_parent as $item) {
            if ($item->parent !== 0) {
                $result_arr['keterangan'] = $item->keterangan;
                $result_arr['amount'] = $item->amount;
                $result_arr['type'] = $item->type;
                $result_arr['style'] = $item->style;
                $result_arr['position'] = $item->position;
                $result_arr['parent'] = $item->parent;
                $result_arr['sum'] = $item->sum;
                $result_arr['operator'] = $item->operator;
                $result_arr['pph'] = $item->pph;
                $result_arr['id'] = $item->id;
                $result_arr['child'] = $this->itemWithChildren($item);

                $result[] = $result_arr;
            }
        }
        return $result;
    }

    private function itemWithChildren($item)
    {
        $result = array();
        $children = $this->childrenOf($item);
        // if(count($children) > 0){
        // }
        foreach ($children as $key => $child) {
            $result_arr['keterangan'] = $child->keterangan;
            $result_arr['amount'] = $child->amount;
            $result_arr['type'] = $child->type;
            $result_arr['style'] = $child->style;
            $result_arr['position'] = $child->position;
            $result_arr['parent'] = $child->parent;
            $result_arr['sum'] = $child->sum;
            $result_arr['operator'] = $child->operator;
            $result_arr['pph'] = $child->pph;
            $result_arr['id'] = $child->id;
            $result_arr['child'] = $this->itemWithChildren($child);
            $result[] = $result_arr;
        }
        return $result;
    }

    private function childrenOf($item)
    {
        $result = array();
        if ($item->parent !== 0) {
            // $result[] = $i;
            $result = ModelsLabaRugi::where('parent', $item->id)->get();
        }
        return $result;
    }

    private function htmlFromArray($array)
    {
        $html = '';
        foreach ($array as $k => $v) {
            $style = '';
            $styleTd = "style='padding: 4px 0 0 48px;'";
            $styleAmount = '';

            switch ($v['style']) {
                case '1':
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '1.1':
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-bottom: 2px solid black;'";
                    break;
                case '1.2':
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;'";
                    break;
                case '1.4':
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '1.5':
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '1.6':
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-bottom: 2px solid black;'";
                    break;
                case '2':
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;text-decoration: underline;'";
                    break;
                case '5':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;text-decoration: underline;'";
                    break;
                case '2.1':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;'";
                    break;
                case '2.6':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;'";
                    break;
                case '3':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;'";
                    break;
                case '3.1':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;border-bottom: 2px solid black;'";
                    break;
                case '2.2':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:capitalize;text-decoration: underline;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '2.3':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-bottom: 2px solid black;'";
                    break;
                case '2.4':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '2.5':
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:capitalize;text-decoration: underline;'";
                    break;
                case '2.7':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:uppercase;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '2.9':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:uppercase;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-bottom: 2px solid black;'";
                    break;
                case '4.4':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:uppercase;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;border-top: 2px solid black;'";
                    break;
                case '4.3':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:uppercase;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 600;'";
                    break;
                case '2.8':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 600;text-transform:uppercase;text-decoration:underline;'";
                    break;
                case '4':
                    $styleTd = "style='padding: 4px 0 0 90px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:uppercase;'";
                    break;
                case '4.1':
                    $styleTd = "style='padding: 4px 0 0 130px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;'";
                    break;
                case '4.7':
                    $styleTd = "style='padding: 4px 0 0 130px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;border-bottom: 2px solid black;'";
                    break;
                case '4.2':
                    $styleTd = "style='padding: 4px 0 0 130px;'";
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform:capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;border-bottom: 2px solid black;'";
                    break;

                default:
                    $style = "style='margin:0;font-size: 14px;font-weight: 500;text-transform: capitalize;'";
                    $styleAmount = "style='margin:0;font-size: 14px;font-weight: 500;text-transform: capitalize;'";
                    break;
            }

            $amount = ($v['amount'] !== '0' && $v['amount'] !== 'floor') ? number_format($v['amount'],0,',','.') : '-';
            $trip = ($v['style'] === '2.1') ? '-' : '';

            $total = 0;
            $totalPPH = 0;

            if ($v['type'] === 1) {
                $query = DB::select('SELECT SUM(amount) as amount FROM laba_rugi WHERE parent = "' . $v['parent'] . '"');

                $total = number_format($query[0]->amount,0,',','.');
            } else if ($v['type'] === 3) {
                $get_parent = explode(',', $v['sum']);
                $operator = explode(',', $v['operator']);
                $amount = 0;
                foreach ($get_parent as $key => $parent) {
                    $query = DB::select('SELECT SUM(amount) as amount FROM laba_rugi WHERE type = "0" AND parent = "' . $parent . '"');

                    if ($operator[$key] === '+') {
                        $amount += $query[0]->amount;
                    } else if ($operator[$key] === '-') {
                        $amount -= $query[0]->amount;
                    } else {
                        $amount *= $query[0]->amount;
                    }
                }

                $total = number_format(abs($amount),0,',','.');
            } else if ($v['type'] === 4) {
                $get_parent = explode(',', $v['sum']);
                $operator = explode(',', $v['operator']);
                $amount = 0;
                foreach ($get_parent as $key => $parent) {
                    $query = DB::select('SELECT SUM(amount) as amount FROM laba_rugi WHERE type = "0" AND id = "' . $parent . '"');

                    if ($operator[$key] === '+') {
                        $amount += $query[0]->amount;
                    } else if ($operator[$key] === '-') {
                        $amount -= $query[0]->amount;
                    } else {
                        $amount *= $query[0]->amount;
                    }
                }

                $total = number_format(abs($amount),0,',','.');
            } else if ($v['type'] === 5) {
                $get_parent_ex = explode(';', $v['sum']);
                $operator_ex = explode(';', $v['operator']);
                $amount = 0;
                foreach ($get_parent_ex as $key => $parent) {
                    if ($key === 0) {
                        $query_1 = 0;
                        $get_parent = explode(',', $parent);
                        $operator = explode(',', $operator_ex[$key]);
                        foreach ($get_parent as $_key => $_parent) {
                            $query = DB::select('SELECT SUM(amount) as amount FROM laba_rugi WHERE type = "0" AND id = "' . $_parent . '"');

                            if ($operator[$_key] === '+') {
                                $query_1 += $query[0]->amount;
                            } else if ($operator[$_key] === '-') {
                                $query_1 -= $query[0]->amount;
                            } else {
                                $query_1 *= $query[0]->amount;
                            }
                        }
                        $amount += abs($query_1);
                    } else {
                        $query_2 = 0;
                        $get_parent = explode(',', $parent);
                        $operator = explode(',', $operator_ex[$key]);
                        foreach ($get_parent as $_key => $_parent) {
                            $query = DB::select('SELECT SUM(amount) as amount FROM laba_rugi WHERE type = "0" AND parent = "' . $_parent . '"');

                            if ($operator[$_key] === '+') {
                                $query_2 += $query[0]->amount;
                            } else if ($operator[$_key] === '-') {
                                $query_2 -= $query[0]->amount;
                            } else {
                                $query_2 *= $query[0]->amount;
                            }
                        }
                        $amount += abs($query_2);
                    }
                }
                if($v['amount'] === 'floor'){
                    $total = number_format(round((int)$amount,-3),0,',','.');
                }else{
                    $total = number_format($amount,0,',','.');
                }
            } else {
                $total = $amount;
            }

            $total = ($v['style'] === '3' || $v['pph'] === 'min') ? '(' . $total . ')' : $total;

            if($v['pph'] !== null){
                if(strpos($v['pph'], '%') !== false){
                    $pph = str_replace('%','',$v['pph']) / 100;
                    $countTotal = ceil(str_replace('.','',$amount) * $pph);
                    $totalPPH = $countTotal;
                }else if(strpos($v['pph'], ',') !== false){
                    $getCount = explode(',',$v['pph']);
                    $totalAllPPh = 0;
                    foreach($getCount as $gc){
                        $query = DB::select('SELECT pph,SUM(amount) as amount FROM laba_rugi WHERE id = "' . $gc . '"');
                        $pph = str_replace('%','',$query[0]->pph) / 100;
                        $countTotal = ceil($query[0]->amount * $pph);

                        $totalAllPPh += $countTotal;
                    }
                    if ($v['type'] === 5 || $v['type'] === 4){
                        $total = number_format(abs(str_replace('.','',$total) - $totalAllPPh),0,',','.');
                    }else{
                        $total = number_format($totalAllPPh,0,',','.');
                    }
                }
            }

            if (
                $v['style'] === '2' || $v['style'] === '2.3' || $v['style'] === '2.4' || $v['style'] === '2.5' ||
                $v['style'] === '2.8' || $v['style'] === '1.2'|| $v['style'] === '5'
            ) {
                $html .= '<tr>
                    <td></td>
                    <td width="120"></td>
                </tr>';
            }
            $html .= "<tr style='line-height: 14px;'>";
            if($v['position'] === 'end-end'){
                $html .= "<td style='text-align:right;' colspan='2'>
                    <h4 $style>
                        " . $trip . $v['keterangan'] . "
                    </h4>
                </td>";
                $html .= "<td style='text-align: right;padding: 4px 0 0 48px;' width='150'>
                    <h4 $styleAmount>
                        " . $total . "
                    </h4>
                </td>";
            }else if($v['position'] === 'center-end'){
                $html .= "<td $styleTd width='450'>
                    <h4 $style>
                        " . $trip . $v['keterangan'] . "
                    </h4>
                </td>";
                $html .= "<td style='text-align: right;padding: 4px 0 0 48px;' width='150'>
                    <h4 $styleAmount>
                        " . $total . ' X ' . $v['pph'] . "
                    </h4>
                </td>";
                $html .= "<td style='text-align: right;padding: 4px 0 0 48px;' width='150'>
                    <h4 $styleAmount>
                        " . number_format($totalPPH,0,',','.') . "
                    </h4>
                </td>";
            }else{
                $html .= "<td $styleTd width='450'>
                        <h4 $style>
                            " . $trip . $v['keterangan'] . "
                        </h4>
                    </td>";
                if ($v['style'] !== '2.5' && $v['style'] !== '2' && $v['style'] !== '2.8' && $v['style'] !== '4' &&
                $v['style'] !== '5') {
                    if ($v['position'] === 'center') {
                        $html .= "<td style='text-align: right;padding: 4px 0 0 48px;' width='150'>
                                <h4 $styleAmount>
                                    " . $total . "
                                </h4>
                            </td>";
                        $html .= '<td width="250"></td>';
                    } else {
                        $html .= '<td width="250"></td>';
                        $html .= "<td style='text-align: right;padding: 4px 0 0 48px;' width='150'>
                                <h4 $styleAmount>
                                    " . $total . "
                                </h4>
                            </td>";
                    }
                }
            }
            $html .= "</tr>";
            if (count($v['child']) > 0) {
                $html .= "<tr style='line-height: 14px;'>";
                $html .= $this->htmlFromArray($v['child']);
                $html .= "</tr>";
            }
        }
        return $html;
    }
}
