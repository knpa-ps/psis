@extends('layouts.master')
@section('style')
{{ HTML::style('assets/css/jquery.treeview.css') }}
<style type="text/css">
    body {
        padding: 5px;
        min-width: 0;
    }
    .container {
        padding: 0px;
        width: 470px !important;
        min-width: 0px !important;
    }
    #view-container {
        height:770px;
        overflow-y: scroll;
    }
    .panel-heading {
        font-weight: bold;
    }
    .tab-pane {
      padding-top: 10px;
    }
</style>
@stop

@section('content')
<div class="panel panel-default" id="view-container">
  <div class="panel-heading">
    {{ $title }}
  </div>
  <div class="panel-body">
<!--     <div class="row">
        <div class="col-xs-7">
          <div class="input-group">
            <input type="text" class="form-control" id="q" name="q">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" id="search">@lang('strings.search')</button>
            </span>
          </div>
        </div>
    </div>
    <hr> -->
    <div class="row">
        <div class="col-xs-12"> 

            <ul class="nav nav-tabs" id="dept-tab">
              <li class="active"><a href="#pane-dept-tree" data-toggle="tab">@lang('strings.dept_tree')</a></li>
              <!-- <li><a href="#pane-search-result" data-toggle="tab">@lang('strings.dept_search_result')</a></li> -->
            </ul>
            <div class="tab-content">
                  <div class="tab-pane active" id="pane-dept-tree">
                      <div id="initial-loader" class="treeview"><span class="placeholder" style="width:100%; padding-left:16px;">@lang('strings.wait')</span></div>
                      <ul id="dept-tree">
  
                      </ul>
                  </div>
 <!--                  <div class="tab-pane" id="pane-search-result">
                      <table class="table table-condensed table-hover table-striped">
                      <thead>
                          <th>
                              sdf
                          </th>
                      </thead>
                      <tbody>
                          <tr>
                              <td>
                                  sdf
                              </td>
                          </tr>
                      </tbody>
                      </table>
                  </div> -->
            </div>

        </div>
    </div>
  </div>
</div>
@stop

@section('script')
{{ HTML::script('assets/js/vendor/jquery.treeview.js') }}
{{ HTML::script('assets/js/vendor/jquery.treeview.async.js') }}
{{ HTML::script('assets/js/vendor/jquery.treeview.edit.js') }}
<script type="text/javascript">
    $(document).ready(function(){ 
        $('#dept-tab a').click(function (e) {
          e.preventDefault()
          $(this).tab('show');
        });

        $("#dept-tree").treeview({
            url: '{{ action('DepartmentController@getChildren') }}',
            collapsed: true,
            ajax: {
              complete: function() {
                $("#initial-loader").remove();
              }
            }
        });
    });
    $(document).on('click', 'a.tree-text', function(e){
            var deptId = $(this).parent().prop('id');
            var deptName = $("#"+deptId+"-data").val();
            window.opener.setDept(deptId, deptName);
            window.close();
            return false;
        });
</script>
@stop