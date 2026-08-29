@if ($paginator)
	<!--<div class="tab-pagination">-->
	<!--	<ul>-->
	<!--		{{-- Previous Page Link --}}-->
	<!--		@if ($paginator->currentPage() != 1)-->
	<!--			<li><a href="javascript:void(0);"><i class="icon-angle-left"></i></a></li>-->
	<!--		@else-->
	<!--			<li><a href="{{ $paginator->url(1) }}"><i class="icon-angle-left"></i></a></li>-->
	<!--		@endif-->

	<!--		@for ($i = 1; $i <= $paginator->lastPage(); $i++)-->
	<!--			<li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }}">-->
	<!--				<a href="{{ $paginator->url($i) }}">{{ $i }}</a>-->
	<!--			</li>-->
 <!--   		@endfor-->

	<!--		{{-- Next Page Link --}}-->
	<!--		<li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">-->
	<!--			<a href="{{ $paginator->url($paginator->currentPage()+1) }}"><i class="icon-angle-right"></i></a>-->
	<!--		</li>-->
	<!--	</ul>-->
	<!--</div>-->
	
	<nav aria-label="Page navigation example">
      <ul class="pagination">
         @if ($paginator->currentPage() != 1)
            <li class="page-item"><a class="page-link" href="javascript:void(0);">Previous</a></li>
        @else
            <li class="page-item"><a class="page-link" href="{{ $paginator->url(1) }}">Previous</a></li>
        @endif
        
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <li class="page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}"><a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
        @endfor
        
        <li class="page-item {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}"><a class="page-link" href="{{ $paginator->url($paginator->currentPage()+1) }}">Next</a></li>
      </ul>
    </nav>
@endif


