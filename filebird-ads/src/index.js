import './styles/styles.scss'

const fbv_ads = `<div id="filebird_ads" class="fbv-ads-wrap">
  <div class="fbv-ads-popup">
    <div class="fbv-ads-icon-wrap">
      <i class="fbv-icon fbv-i-folder"></i>
      <i class="dashicons dashicons-no-alt"></i>
    </div>
    <div class="fbv-ads-sub">
      <span>Organize your files</span>
    </div>
  </div>
  <div class="fbv-ads-window">
    <div class="fbv-ads-window-mess">
      <h3>Your WordPress media library is messy?</h3>
      <span>Start using FileBird to organize your files into folders by drag and drop.</span>
    </div>
    <div class="fbv-ads-window-img-wrap">
      <img src="https://ps.w.org/filebird/assets/screenshot-2.gif" alt="screenshot_demo">
    </div>
    <div class="fbv-ads-window-btn">
      <div><a class="button button-primary fbv-ads-install" href="#"><i class="dashicons dashicons-wordpress-alt"></i>Install for free</a></div>
      <div><a class="fbv-ads-link" href="javascript:void(0)">Don't display again</a></div>
    </div>
  </div>
</div>`

jQuery(document).ready(function () {
  jQuery.fn.exists = function (callback) {
    var args = [].slice.call(arguments, 1)
    if (this.length) {
      callback.call(this, args)
    }
    return this
  }
  jQuery('#wpcontent').exists(function () {
    this.append(fbv_ads)
  })
  jQuery('.fbv-ads-popup').click(function () {
    jQuery(this).parent().toggleClass('fbv-ads-popup-open')
  })
  jQuery('.fbv-ads-link').click(function () {
    const a = jQuery('#filebird_ads')
    a.removeClass('fbv-ads-popup-open').addClass('fbv_permanent_hide')
    setTimeout(function () {
      a.remove()
    }, 2000)
  })
  jQuery('.fbv-ads-install:not(.fbv_installing)').click(function (e) {
    e.preventDefault()
    const normal = '<i class="dashicons dashicons-wordpress-alt"></i>Install for free'
    const loading = '<i class="dashicons dashicons-update-alt"></i>Installing<span class="text-dots"><span>.<span></span>'
    const done = '<i class="dashicons dashicons-saved"></i>Installed'
    const error = '<i class="dashicons dashicons-warning"></i>Install failed'
    const a = jQuery(this)
    a.focusout()
    a.addClass('fbv_installing')
    a.html(loading)
    setTimeout(function () {
      a.removeClass('fbv_installing').addClass('fbv_done')
      a.html(done)
    }, 3000)
  })
})
