<?php
/**
 * Query Report shortcode
 *
 * Write [wqr] in your post editor to render this shortcode.
 *
 * @package	 ABS
 * @since    1.0.0
 */

if ( ! function_exists( 'wqr_report_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'wqr', 'wqr_report_shortcode' );
	});

	/**
	 * Report shortcode.
	 *
	 * @return string Shortcode output string.
	 * @since  1.0.0
	 */
	function wqr_report_shortcode() {

		if ( ! current_user_can( 'administrator' ) ) {
			wp_redirect( get_site_url() );
			exit;
			// wp_die();
		}

		global $wpdb;

		$start_date = ( isset( $_GET['start_date'] ) ) ? sanitize_text_field( $_GET['start_date'] ) : '';
		$end_date   = ( isset( $_GET['end_date'] ) ) ? sanitize_text_field( $_GET['end_date'] ) : '';

		$sql = "SELECT SQL_CALC_FOUND_ROWS q.query, q.query_id, MIN(h.date) min_date, MAX(h.date) max_date, COUNT(DISTINCT h.ip) hits FROM wp_wps_query q LEFT JOIN wp_wps_hit h ON q.query_id = h.query_id ";
		if ( $start_date || $end_date ) {
			$sql .= "where 1=1 ";
			if ( $start_date ) {
				$sql .= "and h.date >= '$start_date' ";
			}
			if ( $end_date ) {
				$sql .= "and h.date <= '$end_date' ";
			}
		}
		$sql .= "GROUP BY q.query_id ORDER BY hits DESC;";
		$results = $wpdb->get_results( $sql );
		$cols = [
			[
				'slug' => 'query',
				'label' => 'Query',
			],
			// 'query_id',
			[
				'slug' => 'min_date',
				'label' => 'First',
			],
			[
				'slug' => 'max_date',
				'label' => 'Last',
			],
			[
				'slug' => 'hits',
				'label' => 'Hits',
			],
		];
		?>
		<h3>Date range</h3>
		<div id="query-loading">Loading...</div>
		
		<div id="query-container" style="display:none;">
			<form>
				<?php if ( is_admin() ) { ?>
					<input type="hidden" name="page" value="jhl_wqr">
				<?php } ?>
				<table class="form-table" role="presentation" style="width:auto;">
					<tr>
						<th style="width:auto;">
							<label for="startDate">From</label>
						</th>
						<td>
							<input id="startDate" type="date" name="start_date" <?php if ( $start_date ) { echo "value='$start_date'"; } ?>>
						</td>
					</tr>
					<tr>
						<th style="width:auto;">
							<label for="endDate">To</label>
						</th>
						<td>
							<input id="endDate" type="date" name="end_date" <?php if ( $end_date ) { echo "value='$end_date'"; } ?>>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="Filter">
				</p>
			</form>

			<table id="query-report">
				<thead>
					<?php foreach( $cols as $th ) { ?>
						<th><?php echo $th['label']; ?></th>
					<?php } ?>
				</thead>
				<tbody>
					<?php foreach( $results as $tr ) { ?>
						<tr>
							<?php foreach( $cols as $td ) { ?>
								<td><?php echo $tr->{$td['slug']}; ?></td>
							<?php } ?>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<!-- include datatable scripts -->
		<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
		<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

		<script src="//cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
		<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
		<script src="//cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js "></script>

		<!-- activate it -->
		<script>
		jQuery(document).ready( function () {
			jQuery('#query-report').DataTable({
				dom: 'Blfrtip',
				<?php if ( is_admin() ) { ?>
					buttons: [
						{
							extend: 'copy',
							className: 'button button-secondary',
						},
						{
							extend: 'csv',
							className: 'button button-secondary',
						},
						{
							extend: 'excel',
							className: 'button button-secondary',
						},
						{
							extend: 'pdf',
							className: 'button button-secondary',
						},
						{
							extend: 'print',
							className: 'button button-secondary',
						},
					],
				<?php } else { ?>
					buttons: [
						'copy', 'csv', 'excel', 'pdf', 'print'
					],
				<?php } ?>
				"order": [[ 3, 'desc' ]],
				"initComplete": function(settings, json) {
					document.getElementById('query-loading').style.display = 'none';
					document.getElementById('query-container').style.display = 'block';
				}
			});

			check_date_fields = function() {
				var start_date = jQuery('input[name=start_date]');
				var end_date = jQuery('input[name=end_date]');
				if ( end_date.val() ) {
					if ( end_date.val() < start_date.val() ) {
						end_date.val( start_date.val() );
					}
				}
			};

			// set end date based on start date
			jQuery('input[name=start_date]').on('change', function() {
				check_date_fields();
			});
			jQuery('input[name=end_date]').on('change', function() {
				check_date_fields();
			});
		} );
		</script>

		<style>
			.dataTables_wrapper .dt-buttons::before {
				content     : "Export Report";
				font-size   : 14px;
				padding-top : 5px;
				display     : inline-block;
				font-weight : bold;
				margin-right: 10px;
			}
		</style>
		<?
	}
}
