@extends('layouts.master')

@section('styles')
	{{HTML::style('static/vendor/jstree/themes/default/style.min.css')}}
@stop

@section('content')
	<div id="jstree_demo" class="span5"></div>
  <div id="treeitem_info" class="box span5">
    <div class="box-header well">
      <h2 id="selected_item">
        <i class="icon-zoom-in"></i>
        <span id="fullname"></span>
      </h2>
    </div>
    <div class="box-content">
      <p>
        <h4 id="detail"></h4>
        <span id="isalive"></span>
        <span id="isterminal"></span>
        <span id="sortorder"></span>
      </p>
    </div>
  </div>
@stop

@section('scripts')
	{{ HTML::script('static/vendor/jstree/jstree.min.js')}}
<script>
$(function() {
      $('#jstree_demo')
      .on('changed.jstree', function (e, data) {
        var id = [];
        id.push({"name":"id", "value": data.instance.get_node(data.selected).id });
        $.ajax({
          url : "{{ action('DepartmentController@getFullName') }}",
          type : "post",
          data : id,
          success : function(response){
            $('#fullname').html(response[0]);
            $('#detail').html("부서 세부정보");
            $('#isalive').html("is_alive="+response[1]+"<br>");
            $('#isterminal').html("is_terminal="+response[2]+"<br>");
            $('#sortorder').html("sort_order="+response[3]+"<br>");
          },
          error : function(){
            alert("서버 오류");
          }
        });
      })
      .jstree({
    "core" : {
      "animation" : 200,
      "multiple" : false,
      "check_callback" : function (operation, node, node_parent, node_position, more) {
        return true;
      },
      "themes" : { "stripes" : true },
      'data' : {
        'url' : function (node) {
          var id = node.id;
          if (id==='#') {
            return '{{ url('depts/0/children') }}';
          }
          return 'http://localhost/psis/depts/'+id+'/children';
        }
      }
    },
      "types" : {
        "#" : {
          "valid_children" : ["root"]
        },
        "root" : {
          "valid_children" : ["default"]
        },
        "default" : {
          "valid_children" : ["default","file"]
        },
        "file" : {
          "icon" : "glyphicon glyphicon-file",
          "valid_children" : []
        }
      },
      "plugins" : [
        "dnd", "wholerow"
      ]
    });
});
</script>
@stop