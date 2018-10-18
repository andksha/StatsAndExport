@extends('layouts.default')

@section('content')
    <div class='container'>
        @if (session('error'))
            <div class='row text-center'>
                <p id='message'>
                    {{ session('error') }}
                </p>
            </div>
        @endif
        <div class='row'>
            <h4 class="caption text-center">
                {{ trans('exchange.export.choose_data') }}
            </h4>
            <div class='row exportSelect'>
                <div class="labSel">
                    <label> Вид:</label>
                    <select name="p_layouts">
                        <option value="1">Короткий</option>
                        <option value="2" selected="selected">Повний</option>
                    </select>
                </div>
                <div class="labSel">
                    <label> Формат:</label>
                    <select name="p_formats" class="vSelect">
                        <option value="docx" selected="selected">.docx</option>
                        <option value="pdf">.pdf</option>
                        <option value="html">.html</option>
                    </select>
                </div>
            </div>
        <div class='row'>
            <div class='col-md-12 text-center'>
                <a href='#' class='btn btn-default export'>
                    {{ trans('exchange.export.name') }}
                </a>
            </div>
        </div>
    </div>
@endsection