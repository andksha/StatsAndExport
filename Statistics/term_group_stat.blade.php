<div class='container stats' style='width:90%;'>
    <div class='row caption'>
        <h4 class='caption'>{{ trans('stats.captions.term_voting') }}</h4>
    </div>
    <div class='row'>
        <div class='col-md-2 caption'>{{ trans('grid.terms.term') }}</div>
        <div class='col-md-2 caption'>{{ trans('grid.terms.source') }}</div>
        <div class='col-md-2 caption'>{{ trans('grid.terms.comm') }}</div>
        <div class='col-md-2 caption'>{{ trans('stats.captions.author') }}</div>
        <div class='col-md-1 caption'>{{ trans('stats.captions.votes_up') }}</div>
        <div class='col-md-2 caption'>{{ trans('stats.captions.votes_down') }}</div>
        <div class='col-md-1 caption'>{{ trans('stats.captions.viewed') }}</div>
    </div>
    @foreach ($term_group as $t_g)
        <div class='row'>
            <div class='col-md-2'>
                {{ link_to('/terms?term=' . $t_g->term, $t_g->term) }}
            </div>
            <div class='col-md-2 text-center'>{{ $t_g->source }}</div>
            <div class='col-md-2'>{{ $t_g->comm }}</div>
            <div class='col-md-2 text-center'>{{ $t_g->last_name }}</div>
            <div class='col-md-1 text-center'>{{ $t_g->votes_up }}</div>
            <div class='col-md-2 text-center'>{{ $t_g->votes_down }}</div>
            <div class='col-md-1 text-center'>{{ $t_g->votes_viewed }}</div>
        </div>
    @endforeach
</div>

