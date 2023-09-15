<?php

use Illuminate\Support\Facades\DB;

  class ItemsHelper {

    private $items;

    public function __construct($items) {
      $this->items = $items;
    }

    public function htmlList() {
        // return $this->itemArray();
      return $this->htmlFromArray($this->itemArray());
    }

    private function itemArray() {
      $result = array();
      foreach($this->items as $item) {
        if ($item->parent !== '') {
            $result[$item->name] = $this->itemWithChildren($item);
        }
      }
      return $result;
    }

    private function childrenOf($item) {
      $result = array();
      foreach($this->items as $i) {
        if ($i->parent !== '') {
            // $result[] = $i;
            $result = DB::select("SELECT * FROM ec_customers WHERE parent='".$item->id."'");
        }
      }
      return $result;
    }

    private function itemWithChildren($item) {
        $result = array();
        $children = $this->childrenOf($item);
        foreach ($children as $key=>$child) {
            $result[$child->name] = $this->itemWithChildren($child);
        }
        return $result;
    }

    private function htmlFromArray($array) {
      $html = '';
      foreach($array as $k=>$v) {
        // $html .= "<ul>";
        $html .= "<li>
            <div class='sticky'>".$k."</div>";
            if(count($v) > 0) {
                $html .= '<ul>';
                $html .= $this->htmlFromArray($v);
                $html .='</ul>';
            }
        $html .= "
        </li>";
      }
      return $html;
    }
  }
