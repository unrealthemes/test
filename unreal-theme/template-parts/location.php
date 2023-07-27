<?php 
$params = $args['params'];
$locations = get_terms( 'location', [
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => 1,
] ); 
?>

<div class="filter__row health-topics">
    <span class="filter__title"><?php _e('Location', 'unreal-themes'); ?></span>
    <div class="filter__control">
        <select id="location" name="location[]">
            <option value><?php _e('Select location', 'unreal themes'); ?></option>

            <?php 
            foreach ( $locations as $key => $location ) : 
                $selected = ( isset($params['location']) && in_array($location->slug, $params['location']) ) ? 'selected' : '';
            ?>
                    
                <option value="<?php echo $location->slug; ?>" <?php echo $selected; ?>>
                    <?php echo $location->name; ?>
                </option>

            <?php endforeach; ?>
        
        </select>
    </div>
</div>