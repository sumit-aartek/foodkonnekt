<div class="fdCategoryContainer">
    <a href="#" class="fdCateItem fdPrev"></a>
    <div class="fdCategoryList">
        <span id="fdCateInner_<?php echo $index; ?>">
            <?php
            foreach ($tpl['main']['category_arr'] as $k => $v) {
                ?><a href="#" class="fdCategoryNode fdCateItem<?php echo $tpl['main']['open_id'] == $v['id'] ? ' fdCateFocus' : null; ?>" data-id="<?php echo $v['id']; ?>"><?php echo pjSanitize::clean($v['name']); ?><label><abbr></abbr></label></a><?php
            }
            ?>
        </span>
    </div>
    <a href="#" class="fdCateItem fdNext"></a>
</div>

<div class="fdProductList">
    <?php
    if (!empty($tpl['main']['product_arr'])) {
        foreach ($tpl['main']['product_arr'] as $product) {
            ?>
            <div id="fdProductBox_<?php echo $product['id']; ?>" class="fdProductBox">
                <form style="overflow: hidden;" action="" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                    <?php
                    $image_path = PJ_INSTALL_URL . PJ_IMG_PATH . 'frontend/no_image.png';
                    if (!empty($product['image'])) {
                        $image_path = PJ_INSTALL_URL . $product['image'];
                    }
					
                    ?>
                    <div class="fdImage" data-id="<?php echo $product['id']; ?>" style="background-image: url('<?php echo $image_path; ?>');"></div>
                    <div class="fdBoxOnRight">
                        <div class="fdBoxHeading">
                            <div class="fdTitle">
                                <a class="fdProductTitle" data-id="<?php echo $product['id']; ?>" href="#"><?php echo pjSanitize::clean($product['name']); ?></a>
                                <div class="fdPrice">
                                    <?php
                                    if ($product['set_different_sizes'] == 'T' && count($product['price_arr']) > 0) {
                                        ?>
                                        <select id="fdSelectSize_<?php echo $product['id']; ?>" name="price_id" class="fdSelect fdW100p fdSelectSize" data-id="<?php echo $product['id']; ?>">
                                            <option value="">-----</option>
                                            <?php
                                            foreach ($product['price_arr'] as $price) {
                                                ?><option value="<?php echo $price['id'] ?>"><?php echo $price['price_name'] ?>: <?php echo pjUtil::formatCurrencySign(number_format($price['price'], 2), $tpl['option_arr']['o_currency']); ?></option><?php
                                            }
                                            ?>
                                        </select>
                                        <?php
                                    } else {
                                        ?><label><?php echo pjUtil::formatCurrencySign(number_format($product['price'], 2), $tpl['option_arr']['o_currency']); ?></label><?php
                                    }
                                    ?>
                                </div>
                            </div>	
                        </div>
                        <div class="fdBoxDesc">
                            <div class="fdDescription"><?php echo nl2br(pjSanitize::clean($product['description'])); ?></div>
                            <div class="fdExtraList">
                                <!-- Manmohan Code here's -->
                                <!-- .tabs -->
                                <?php								
                                if (!empty($product['extra_arr'])) {
                                    /* -------------------------------- */
                                    //Get modify group from db.
                                    $pjModifyGroup = pjModifyGroupModel::factory()
										->where('user_id', $_SESSION['admin_user']['id'])
										->findAll()->getData();                                    
                                    $mg = $mgt = 1;
                                    ?>
                                    <div id="tabs-container">
                                        
                                            <a href="#" id="prev"><</a>
                                            <ul class="tabs-menu slideshow" style="">
                                                <?php
                                                $count_arr = count($pjModifyGroup);
                                                $mg_arr1 = $pjModifyGroup;
                                                for ($rt = 0; $rt <= $count_arr; $rt +=4) {
                                                    ?>
                                                    <li>
                                                        <ul>
                                                            <?php
                                                            $count = 0;
                                                            foreach ($mg_arr1 as $mrow) {
                                                                $mas_tab = multi_array_search($mrow['ModifierGroup_Id'], $product['extra_arr']);
                                                                if ($mas_tab == 1 && $count < 4) {
                                                                    ?>
                                                                    <li class="single-tab-menu">
                                                                        <a name="tab" class="fdTabItem <?php
                                                                        if ($mg == 1) {
                                                                            echo 'current';
                                                                        }
                                                                        ?>" id="tab_<?php echo $product['id']; ?><?php echo $mg ?>" href="javascript:void(0)" onclick="tabs(<?php echo $product['id']; ?><?php echo $mg ?>)"><?php echo $mrow['ModifierGroup_Name'] ?></a>
                                                                    </li>
                                                                    <?php
                                                                    $mg++;
                                                                    $count++;
                                                                }
																array_shift($mg_arr1);
                                                            }
                                                            ?>

                                                        </ul>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                            <a href="#" id="next">></a>
                                        <div class="tab">
                                            <?php
                                            foreach ($pjModifyGroup as $mrow) {
                                                $mas_content = multi_array_search($mrow['ModifierGroup_Id'], $product['extra_arr']);
                                                if ($mas_content == 1) {
                                                    ?>
                                                    <div name="tab_content" id="tab_content_<?php echo $product['id']; ?><?php echo $mgt; ?>" class="tab-content">                                                                        
                                                        <?php
                                                        foreach ($product['extra_arr'] as $extra) {
                                                            if ($mrow['ModifierGroup_Id'] == $extra['ModifierGroup_Id']) {
                                                                ?>

                                                                <div class="fdExtraBox">
                                                                    <label><?php echo pjSanitize::clean($extra['name']); ?></label>
                                                                    <span class="fdExtraPrice"><?php echo pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']); ?></span>
                                                                    <a href="#" class="fdAddExtra" data-index="<?php echo $product['id'] ?>-<?php echo $extra['id']; ?>"><?php __('front_add'); ?></a>
                                                                    <div class="fdExtraQty"><div class="fdSpinner fdLeft"><abbr class="fdOperator" data-index="<?php echo $product['id'] ?>-<?php echo $extra['id']; ?>" data-sign="-">-</abbr></div><div class="fdMiddle"><input id="fdQty_<?php echo $product['id'] ?>-<?php echo $extra['id']; ?>" name="extra_id[<?php echo $extra['id']; ?>]" class="fdQtyInput" value="0"/></div><div class="fdSpinner fdRight"><abbr class="fdOperator" data-index="<?php echo $product['id'] ?>-<?php echo $extra['id']; ?>" data-sign="+">+</abbr></div></div>
                                                                </div>                                                                        
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                        <a id="fdProductOrder_<?php echo $product['id']; ?>" class="fdButton fdAbsoluteButton fdProductOrder" data-id="<?php echo $product['id']; ?>" href="#"><?php __('front_order_now'); ?></a>
                                                    </div>
                                                    <?php
                                                    $mgt++;
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <!-- /.tabs -->
                                <?php } else { ?>
                                    <a id="fdProductOrder_<?php echo $product['id']; ?>" class="fdButton fdProductOrder" data-id="<?php echo $product['id']; ?>" href="#"><?php __('front_order_now'); ?></a>
                                <?php } ?>
                                <!-- Manmohan End of code here's -->
                                <?php
                                /*
                                  if(!empty($product['extra_arr']))
                                  {
                                  foreach($product['extra_arr'] as $extra)
                                  {
                                  ?>
                                  <div class="fdExtraBox">
                                  <label><?php echo pjSanitize::clean($extra['name']); ?></label>
                                  <span class="fdExtraPrice"><?php echo pjUtil::formatCurrencySign(number_format($extra['price'], 2), $tpl['option_arr']['o_currency']);?></span>
                                  <a href="#" class="fdAddExtra" data-index="<?php echo $product['id']?>-<?php echo $extra['id'];?>"><?php __('front_add');?></a>
                                  <div class="fdExtraQty"><div class="fdSpinner fdLeft"><abbr class="fdOperator" data-index="<?php echo $product['id']?>-<?php echo $extra['id'];?>" data-sign="-">-</abbr></div><div class="fdMiddle"><input id="fdQty_<?php echo $product['id']?>-<?php echo $extra['id'];?>" name="extra_id[<?php echo $extra['id'];?>]" class="fdQtyInput" value="0"/></div><div class="fdSpinner fdRight"><abbr class="fdOperator" data-index="<?php echo $product['id']?>-<?php echo $extra['id'];?>" data-sign="+">+</abbr></div></div>
                                  </div>
                                  <?php
                                  }
                                  ?>
                                  <a id="fdProductOrder_<?php echo $product['id']; ?>" class="fdButton fdAbsoluteButton fdProductOrder" data-id="<?php echo $product['id'];?>" href="#"><?php __('front_order_now');?></a>
                                  <?php
                                  }else{
                                  ?>
                                  <a id="fdProductOrder_<?php echo $product['id']; ?>" class="fdButton fdProductOrder" data-id="<?php echo $product['id'];?>" href="#"><?php __('front_order_now');?></a>
                                  <?php
                                  } */
                                ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <?php
        }
    } else {
        __('front_product_not_found', false, false);
    }
    ?>
</div>
<?php
/* custom multi array search function */

function multi_array_search($elem, $array) {
    foreach ($array as $single_array) {
        if ($single_array['ModifierGroup_Id'] === $elem)
            return TRUE;
    }
    return false;
}
?>