<div class="edmStats index sms">
	<div class="min-height">
		<div class="title"><small><?php echo __('Campaign'); ?>:</small> <?php echo $this->Text->truncate(h($campaign['Campaign']['name']), 75, array('exact' => false)); ?>
			<div class="total"><?php echo __('Mobile Sent: %s', number_format($total_sms_stats)); ?></div>
		</div>
		<?php
		if (!empty($sms_stats)) {
			echo $this->element('admin/export_breakdowns', array(
				'controller' => 'sms_stats',
				'action' => 'get_num_sends_export_status',
				'form_action' => 'export_num_sends',
				'export_name' =>  __('Mobile Sent'),
				'export_type' => 'total_sent',
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
					<th width="80" class="text-center"><?php echo __('Mobile');?></th>
					<th width="180" class="text-left"> <?php
						if ($campaign['Campaign']['sms_merge'] == 1) {
							echo __('SMS Content after Merge');
						} else {
							echo __('Content');
						}
						?>
					</th>
					<th width="100" class="text-center"><?php echo __('Delivery Datetime');?></th>
					<th width="80" class="text-center"><?php echo __('Word Count');?></th>
					<th width="100" class="text-center"><a class="tooltip" onmouseout="hideddrivetip()" ;="" onmouseover="ddrivetip('<?php echo __('Original SMS Count &lt;span style=&quot;color: red;&quot;&gt;(+Additional)&lt;/span&gt; &lt;span style=&quot;color: green;&quot;&gt;(-Refund)&lt;/span&gt;'); ?>', 220)"><img alt="tooltip" src="/img/icon-tooltip-right.gif" /></a>
						<?php
						if ($campaign['Campaign']['sms_merge'] == 1) {
							echo __('SMS Count after merge');
						} else {
							echo __('SMS Count');
						}
					?></th>
					<th width="100" class="text-center"><?php echo __('Delivery Status');?></th>
					<th width="80" class="text-center"><?php echo __('Unit Price');?></th>
					<th width="80" class="text-center"><?php echo __('Total Price');?></th>
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
					<td class="text-center"><?php echo $mobile; ?>&nbsp;</td>
					<td><div class="sms-content">
							<?php
							if (isset($sms_stat_contents[$sms_stat_id])) {
							?>
							<div class="wrap" id="wrap_<?php echo $sms_stat_id; ?>">
								<div> <?php echo nl2br(h($sms_stat_contents[$sms_stat_id])); ?> </div>
								<div class="gradient" id="gradient_<?php echo $sms_stat_id; ?>"></div>
							</div>
							<div class="read_more" id="read_more_<?php echo $sms_stat_id; ?>"></div>
							<?php
							}
							?>
						</div></td>
					<td class="text-center"><?php echo $this->Date->solrDatetimeDecode($sms_stat['delivery_datetime'], 'br'); ?></td>
					<td class="text-center"><?php
						echo number_format($sms_stat['word_count']);
						?></td>
					<td class="text-center"><?php
						echo number_format($sms_stat['sms_count']);
						?></td>
					<td class="text-center"><?php echo $this->CodeDesc->getEdmSmsStatStatus($sms_stat); ?></td>
					<td class="text-center"><?php echo $currency_display . number_format($sms_stat['unit_price'], 3); ?></td>
					<td class="text-center"><?php echo $currency_display . number_format($sms_stat['unit_price'] * $sms_count_arr[$sms_stat_id], 3); ?></td>
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
		'url' => $this->Html->url(array('client' => $client_db_name, 'action' => 'view_num_sends', 'campaign_id' => $campaign_id, 'batch_id' => $batch_id, 'mobile' => $search_mobile))
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
	
	var slideHeight = 28; // px
	var def_height = new Array();
	<?php
	foreach ($sms_stats as $sms_stat) {
		echo "var sms_stat_id = " . $sms_stat['sms_stat_id'] . ";\n";
	?>
	
	def_height[sms_stat_id] = $('#wrap_' + sms_stat_id).height();
	//console.log(def_height[sms_stat_id]);
	if (def_height[sms_stat_id] >= slideHeight) {
		$('#wrap_' + sms_stat_id).css('height' , slideHeight + 'px');
		$('#read_more_' + sms_stat_id).append('<a href="javascript:void(0);" id="read_more_a_' + sms_stat_id + '"><?php echo __('. . .'); ?></a>');
		$('#read_more_a_' + sms_stat_id).livequery('click', function(){
			var id = $(this).attr('id');
			var sms_stat_id = id.substr(12);
			
			var curHeight = $('#wrap_' + sms_stat_id).height();
			//console.log("sms_stat_id: " + sms_stat_id);
			if (curHeight == slideHeight) {
				//console.log('increase height to ' + def_height[sms_stat_id]);
				$('#wrap_' + sms_stat_id).animate({
				  height: def_height[sms_stat_id]
				}, "normal");
				$('#read_more_' + sms_stat_id + ' a').html('<?php echo __('. . .'); ?>');
				$('#gradient_' + sms_stat_id).fadeOut();
			} else {
				//console.log('decrease height');
				$('#wrap_' + sms_stat_id).animate({
				  height: slideHeight
				}, "normal");
				$('#read_more_' + sms_stat_id + ' a').html('<?php echo __('. . .'); ?>');
				$('#gradient_' + sms_stat_id).fadeIn();
			}
			return false;
		});
	}
	<?php
	}
	?>
});
</script>