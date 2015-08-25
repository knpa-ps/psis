<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3>관서 선택</h3>
</div>
<div class="modal-body">
	<div id="dept_tree_{{ $container_id }}">

	</div>
</div>

{{ HTML::style('static/vendor/jstree/themes/default/style.min.css') }}
{{ HTML::script('static/vendor/jstree/jstree.min.js') }}

<script type="text/javascript">

$(function() {
	$("#dept_tree_{{ $container_id }}")
	.on('activate_node.jstree', function(e, data) {

		if (!data.node.li_attr["data-selectable"]) {
			$("#dept_tree_{{ $container_id }}").jstree('toggle_node', data.node);
			return;
		}

		var extras = {
			dept_id: data.node.id,
			dept_name: data.node.dept_name,
			full_name: data.node.li_attr["data-full-name"]
		};
		// fire select event!
		$("#{{ $container_id }}").trigger('select.dept-selector', [ extras ]);
		$("#ajax_modal").modal('hide');
	})
	.jstree({
		core: {
			animation: 0,
			check_callback: true,
			themes: { stripes: true },
			data: {
				url: "{{ url('ajax/dept_tree') }}",
				dataType:'json',//내부망에선 이걸 추가해야 돌아감
				data: function (node) {
					return { id: node.id <?php echo (isset($mngDeptId))? ',mngDeptId :'.$mngDeptId : '' ?> };
				}
			}
		},

		plugins: [ "wholerow" ]
	});
});

</script>
