<?php

class PAGING extends JasonGrimes\Paginator
{
    public function __construct(int $totalItems, int $itemsPerPage, int $maxPagesToShow, int $currentPage, string $urlPattern = '')
    {
        parent::__construct($totalItems, $itemsPerPage, $currentPage, $urlPattern);

        $this->setMaxPagesToShow($maxPagesToShow);
    }

    public function toHtml()
    {
        if ($this->numPages <= 1) {
            return '';
        }

        $html = '<ul class="pagination justify-content-center">';
        if ($this->getPrevUrl()) {
            $html .= '<li class="page-item"><a href="' . $this->getPrevUrl() . '" class="page-link">&laquo; '. $this->previousText .'</a></li>';
        }

        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li class="page-item' . ($page['isCurrent'] ? ' active' : '') . '"><a href="' . $page['url'] . '" class="page-link">' . $page['num'] . '</a></li>';
            } else {
                $html .= '<li class="page-item disabled"><span class="page-link">' . $page['num'] . '</span></li>';
            }
        }

        if ($this->getNextUrl()) {
            $html .= '<li class="page_item"><a href="' . $this->getNextUrl() . '" class="page-link">'. $this->nextText .' &raquo;</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
}