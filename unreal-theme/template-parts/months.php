<?php 
$params = $args['params'];
$months = UT_Event::$months;
?>

<div class="filter__row months">
    <span class="filter__title"><?php _e('Months', 'unreal-themes'); ?></span>
    <div class="filter__control">

        <?php 
        foreach ( $months as $month ) : 
            $checked = ( isset($params['months']) && in_array($month, $params['months']) ) ? 'checked' : '';
        ?>

            <div class="filter__input filter__input--icon" data-tax="months" data-id="<?php echo $month; ?>">

                <input type="checkbox" name="months[]" id="<?php echo $month; ?>" value="<?php echo $month; ?>" <?php echo $checked; ?>>

                <label for="<?php echo $month; ?>">
                    <?php echo $month; ?>                
                </label>
            </div>

        <?php endforeach; ?>
        
    </div>
</div>