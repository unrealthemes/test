<?php 
$params = $args['params'];
$years = UT_Event::get_years_array();
?>

<div class="filter__row years">
    <span class="filter__title"><?php _e('Years', 'unreal-themes'); ?></span>
    <div class="filter__control">

        <?php 
        foreach ( $years as $year ) : 
            $checked = ( isset($params['years']) && in_array($year, $params['years']) ) ? 'checked' : '';
        ?>

            <div class="filter__input filter__input--icon" data-tax="years" data-id="<?php echo $year; ?>">

                <input type="checkbox" name="years[]" id="<?php echo $year; ?>" value="<?php echo $year; ?>" <?php echo $checked; ?>>

                <label for="<?php echo $year; ?>">
                    <?php echo $year; ?>                
                </label>
            </div>

        <?php endforeach; ?>
        
    </div>
</div>