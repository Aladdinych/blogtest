<?php
namespace Classes;

class Pagination{

    private $count;
    private $page;
    private $links_perbar;
    private $pages_count;
    private $url;
    private $class;
    private $onclick;
    private $prefix;
    private $first_item;
    private $last_item;
    public $perpage;

    function __construct($count,$page,$url,$perpage=25,$class='',$onclick='',$prefix=''){
        $this->perpage=$perpage;
        $this->links_perbar=4;
        $this->count = $count;
        $this->page = $page;
        $this->url = $url;
        $this->class = $class;
        $this->onclick = (!empty($onclick)) ? 'onclick="'.$onclick.'(#page#);"' : '';
        $this->prefix = $prefix;
        $this->pages_count = intval(ceil($this->count / $this->perpage));
        if($this->links_perbar > $this->pages_count){
            $this->links_perbar = $this->pages_count;
        }
        if($this->page > $this->pages_count){
            $this->page = $this->pages_count;
        }

        // Задать начальный элемент в PaginationBar так, чтобы элемент текущей страницы был посредине
        $this->first_item = $this->page - intval($this->links_perbar / 2);
        if($this->first_item <= 0){
            $this->first_item = 1;
        }
        $this->last_item = $this->first_item + $this->links_perbar - 1;
        if($this->last_item >= $this->pages_count){
            $this->last_item = $this->pages_count;
            $this->first_item = $this->last_item - $this->links_perbar + 1;
        }
    }

    public function getPaginationBar(){

        // Если страница одна, то PaginationBar не нужен
        if ($this->pages_count == 1){
            return false;
        }
        $pagination_bar='';

        if ($this->first_item > 2) {
            $pagination_bar.='<a class="'.$this->class.' first" '.str_replace('#page#','1',$this->onclick).' href="'.$this->url.$this->prefix.'page/1/'.'">|< </a> ';
        }
        if ($this->first_item > 1) {
            $pagination_bar.=' <a class="'.$this->class.' elip" '.str_replace('#page#',($this->first_item - 1),$this->onclick).' href="'.$this->url.$this->prefix.'page/'.($this->first_item - 1).'/"><b class="'.$this->class.'_elip">...</b></a> ';
        }
        for ($item = $this->first_item; $item <= $this->last_item; $item++) {
            if($item == $this->page){
                $pagination_bar.=' <span class="'.$this->class.' cur" '.' ><b class="'.str_replace('#page#',$item,$this->class).'_cur">'.$item.'</b></span> ';
            }else{
                $pagination_bar.=' <a class="'.$this->class.'" '.str_replace('#page#',$item,$this->onclick).' href="'.$this->url.$this->prefix.'page/'.$item.'/">'.$item.'</a> ';
            }
        }
        if (($this->pages_count - $this->last_item) >= 1) {
            $pagination_bar.=' <a class="'.$this->class.' elip" '.str_replace('#page#',($this->last_item + 1),$this->onclick).' href="'.$this->url.$this->prefix.'page/'.($this->last_item + 1).'/"><b class="'.$this->class.'_elip">...</b></a> ';
        }
        if (($this->pages_count - $this->last_item) >= 2) {
            $pagination_bar.=' <a class="'.$this->class.' last" '.str_replace('#page#',$this->pages_count,$this->onclick).' href="'.$this->url.$this->prefix.'page/'.$this->pages_count.'/">>| </a>';
        }
        return $pagination_bar;
    }
}

?>