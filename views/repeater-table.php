<div class="meta">

</div>

<div class="handle">
	<ul class="acf-hl acf-tbody">
		<li class="li-fancyrepeater-order">
			<span class="acf-icon large"><?php echo $i; ?></span>
		</li>
		<li class="li-fancyrepeater-label">
			<strong>
				<a class="edit-field" title="<?php _e( 'Edit', 'acf_child_post_field' ); ?> <?php echo esc_attr( $field['button_label'] ); ?>" href="#"><?php echo $row[$title_input_key]; ?></a>
			</strong>
			
			<div class="row-options">
				<a class="edit-field" title="<?php _e( 'Edit', 'acf_child_post_field' ); ?> <?php echo esc_attr( $field['button_label'] ); ?>" href="#"><?php _e( 'Edit', 'acf_child_post_field' ); ?></a>
				<a class="delete-field acf-fancyrepeater-remove-row" title="<?php _e( 'Delete', 'acf_child_post_field' ); ?> <?php echo esc_attr( $field['button_label'] ); ?>" href="#">| <?php _e( 'Delete', 'acf_child_post_field' ); ?></a>
			</div>
		</li>
		<li class="li-fancyrepeater-name">
		</li>
		<li class="li-fancyrepeater-type">
		</li>	
	</ul>
</div>

<div class="settings">
	<table <?php acf_esc_attr_e( array('class' => "acf-table acf-input-table {$field['layout']}-layout") ); ?>>

		<?php if ( $field['layout'] == 'table' ): ?>
			<thead>
				<tr>
					<?php if ( $show_order ): ?>
						<th class="order"><span class="order-spacer"></span></th>
					<?php endif; ?>

					<?php
					foreach ( $field['sub_fields'] as $sub_field ):

						$atts = array(
						    'class' => "acf-th acf-th-{$sub_field['name']}",
						    'data-key' => $sub_field['key'],
						);


						// Add custom width
						if ( $sub_field['wrapper']['width'] ) {

							$atts['data-width'] = $sub_field['wrapper']['width'];
						}
						?>

						<th <?php acf_esc_attr_e( $atts ); ?>>
							<?php acf_the_field_label( $sub_field ); ?>
							<?php if ( $sub_field['instructions'] ): ?>
					<p class="description"><?php echo $sub_field['instructions']; ?></p>
				<?php endif; ?>
				</th>

			<?php endforeach; ?>

			</tr>	
			</thead>
		<?php endif; ?>

		<tbody>
			<tr class="acf-row">

				<?php if ( $show_order ): ?>
					<td class="order" title="<?php _e( 'Drag to reorder', 'acf' ); ?>"><?php echo intval( $i ) + 1; ?></td>
				<?php endif; ?>

				<?php echo $before_fields; ?>

				<?php
				foreach ( $field['sub_fields'] as $sub_field ):

					// prevent repeater field from creating multiple conditional logic items for each row
					if ( $i !== 'acfcloneindex' ) {

						$sub_field['conditional_logic'] = 0;
					}


					// add value
					if ( isset( $row[$sub_field['key']] ) ) {

						// this is a normal value
						$sub_field['value'] = $row[$sub_field['key']];
					} elseif ( isset( $sub_field['default_value'] ) ) {

						// no value, but this sub field has a default value
						$sub_field['value'] = $sub_field['default_value'];
					}


					// update prefix to allow for nested values
					$sub_field['prefix'] = "{$field['name']}[{$i}]";


					// render input
					acf_render_field_wrap( $sub_field, $el );
					?>

				<?php endforeach; ?>

				<?php echo $after_fields; ?>


			</tr>
		</tbody>
	</table>
</div>