<div class="edmStats index sms">
	<div class="min-height">
		<div class="title"><small><?php echo __('Campaign'); ?>:</small> <?php echo $this->Text->truncate(h($campaign['Campaign']['name']), 75, array('exact' => false)); ?>
			<div class="total"><?php echo __('Not Subscribed: %s', number_format($total_sms_stats)); ?></div>
		</div>
		<?php
		if (!empty($sms_stats)) {
			echo $this->element('admin/export_breakdowns', array(
				'controller' => 'sms_stats',
				'action' => 'get_sms_bk_ns_export_status',
				'form_action' => 'export_bk_ns_sends',
				'export_name' =>  __('Not Subscribed'),
				'export_type' => 'sms_bk_ns_sent',
				'export_count' => $total_sms_stats
			));
		}
		?>
		<div id="filter-div">
			<fieldset>
				<?php echo $this->element('admin/sms_stat_filter_form'); ?>
			</fieldset>
		</div>
		<table cellpadding="0" cellspacing="0" class="popup-table table table-striped">
			<thead>
				<tr>
					<?php
					if ($globalconf['GL.useMemberId'] == 1) {
					?>
					<th width="200" class="text-center"><?php echo __('Member ID');?></th>
					<?php
					}
					?>
					<?php
					if ($allow_duplicate) {
					?>
					<th width="50" class="text-center"><?php echo __('Excel Row No.');?></th>
					<?php
					}
					?>
					<th width="80" class="text-center"><?php echo __('Country Code');?></th>
					<th width="100" class="text-center"><?php echo __('Mobile');?></th>
					<th width="100" class="text-center"><?php echo __('Delivery Datetime');?></th>
					<th width="560" class="text-left"><?php echo __('Description');?></th>
					<th width="100" class="text-center"><?php echo __('Stat. Datetime');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($sms_stats as $sms_stat) {
					$sms_stat_id = $sms_stat['sms_stat_id'];
					
					if ($globalconf['GL.useMemberId'] == 1) {
						if (strpos($sms_stat['mobile'], '||') !== false) {
							list($member_id, $country_code_mobile) = explode('||', $sms_stat['mobile']);
						} else {
							$member_id = '';
							$country_code_mobile = $sms_stat['mobile'];
						}
					} else {
						$country_code_mobile = $sms_stat['mobile'];
					}
					
					list($country_code, $mobile) = explode('.', $country_code_mobile);

					if ($allow_duplicate) {
						if (strpos($country_code, '||') !== false) {
							list($row_no, $country_code) = explode('||', $country_code);
						} else {
							$row_no = '';
						}
					}
				?>
				<tr>
					<?php
					if ($globalconf['GL.useMemberId'] == 1) {
					?>
					<td class="text-center"><?php echo $member_id; ?>&nbsp;</td>
					<?php
					}
					?>
					<?php
					if ($allow_duplicate) {
					?>
					<td class="text-center"><?php echo $row_no; ?>&nbsp;</td>
					<?php
					}
					?>
					<td class="text-center"><?php echo $country_code; ?>&nbsp;</td>
					<td class="text-center"><?php echo empty($mobile) ? "&lt;" . __('Empty Mobile') . "&gt;" : $mobile; ?>&nbsp;</td>
					<td class="text-center"><?php echo $this->Date->solrDatetimeDecode($sms_stat['delivery_datetime'], 'br'); ?></td>
					<td><?php echo $this->CodeDesc->getEdmSmsStatDesc($sms_stat); ?>&nbsp;</td>
					<td class="text-center"><?php echo $this->Date->solrDatetimeDecode($sms_stat['stat_datetime'], 'br'); ?></td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
	echo $this->element('admin/solr_index_paging', array(
		'page_start' => $solr_start + 1,
		'page_end' => $solr_limit * ($page - 1) + count($sms_stats),
		'page_item_total' => $solr_limit,
		'item_total' => $total_sms_stats,
		'current_page' => $page,
		'url' => $this->Html->url(array('client' => $client_db_name, 'action' => 'view_bk_ns_sends', 'campaign_id' => $campaign_id, 'batch_id' => $batch_id, 'mobile' => $search_mobile))
	));
	?>
</div>
<script type="text/javascript" src="/js/tooltips.js"></script>
<script type="text/javascript">
$(function() {
	<?php
	if (empty($sms_stats)) {
	?>
	$(".closeBtn").click(function() {
		parent.$.fancybox.close();
	});
	<?php
	}
	?>
});
</script>