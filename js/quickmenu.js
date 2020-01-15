// https://civicrm.org/licensing
(function($, _) {
  "use strict";
  CRM.quickmenubar = _.extend({
    data: null,
    attachTo: 'body',
    initialize: function () {
      var cache = CRM.cache.get('quickmenubar');
      var now = new Date().getTime();
      var hours = 1; // Reset when storage is more than 1 hour
      if (cache &&
        cache.locale === CRM.config.locale &&
        cache.cid === CRM.config.cid && localStorage.civiQuickMenubar &&
        cache.expiryTime > now
      ) {
        CRM.quickmenubar.data = cache.data;
        render(localStorage.civiQuickMenubar);
      } else {
        $.getJSON(CRM.url('civicrm/ajax/quickmenujs', {locale: CRM.config.locale, cid: CRM.config.cid}))
          .done(function (data) {
            var markup = data.quickmenu;
            var newExpiryTime = now + (hours * 60 * 60 * 1000)
            CRM.cache.set('quickmenubar', {
              locale: CRM.config.locale,
              cid: CRM.config.cid,
              data: markup,
              expiryTime: newExpiryTime
            });
            CRM.quickmenubar.data = markup;
            localStorage.setItem('civiQuickMenubar', markup);
            render(markup);
          });
      }

      function render(markup) {
        var position = CRM.quickmenubar.attachTo === 'body' ? 'beforeend' : 'afterbegin';
        $(CRM.quickmenubar.attachTo)[0].insertAdjacentHTML(position, markup);
        otherFunction()
      }
    }
  }, CRM.quickmenubar || {});
  CRM.quickmenubar.initialize();
})(CRM.$, CRM._);


function otherFunction() {
  cj('ul#civicrm-menu').append('<li id="crm-qukckmenu" class="menumain" tabindex="20"><div id="form-quickmenu"><div style="position:relative;"><input style="width:6em;" type="text" name="quickmenu" placeholder="Quick Menu Search" id="quickmenu" class="form-text" maxlength="64"><input id="quickmenu-reset"  class="quickmenu-reset" type="reset" value="X" /></div></div></li>');

  cj(".quickmenu-reset").click(function () {
    // close popup box
    cj(this).popupClose();
  });
  cj("#quickmenu").focusin(function () {
    // widen the search box
    cj("#quickmenu").css('width', '12em');
    // show close icon
    cj("#quickmenu-reset").show();
  }).focusout(function () {
    if (cj("#quickmenu").val().length == '0') {
      // if search string is empty hide the box
      cj("#quickmenu").css('width', '6em');
      cj("#quickmenu-reset").hide();
      cj(this).popupClose();
    }
  });

  cj.fn.popupClose = function () {
    cj("body").removeClass("popup-open")
    cj(".overlay-quick-menu").removeClass("popup-open");
    cj('#civicrm-menu-custom').hide();
    cj('#overlay-quick-menu').hide();
    cj("#quickmenu").val('');
  };
  // Get all li with anchor tag
  var labels = cj('#civicrm-menu-custom li a');

  // Flag to show hide message.
  var matchString = false;
  var showNomsg = false;

  cj('#quickmenu').keyup(function (e) {
    // Minimua 3 char requick to lookup menu
    if (this.value.length < 3) return;
    if (e.keyCode == 27) {
      cj(this).popupClose();
      return false;
    }

    cj('#civicrm-menu-custom').show();
    cj('#overlay-quick-menu').show();
    // Open Dialog box
    //$('#overlay-quick-menu').dialog({title: 'Quick Menu Search',resizable: false, height: 400});
    cj("body").addClass("popup-open").fadeIn(2000);
    cj(".overlay-quick-menu").addClass("popup-open").fadeIn(400);
    e.preventDefault();
    // slow all menu text
    var valThis = cj(this).val().toLowerCase();
    var matchString = false;
    var showNomsg = false
    // if input is empty then hide content in popup box.
    if (valThis == "") {
      cj('#civicrm-menu-custom').hide();
    } else {
      // iterate each menu to match input string
      labels.each(function () {
        var label = cj(this); // cache this
        // hide any bullet style to li
        cj(this).parent().css('list-style', 'none');
        var text = label.text().toLowerCase();
        if (text.indexOf(valThis) > -1) {
          matchString = true;
          showNomsg = true;
          // show all li parents up the ancestor tree
          label.parents('li').show();
        } else {
          // hide current li as it doesn't match
          label.parent().hide();
        }
      });
    }

    // This block to show 'Not Match' Message instead of showing empty popup box.
    if (cj('#civicrm-menu-custom').css('display') == 'block' && !matchString && !showNomsg) {
      showNomsg = true;
      cj('#quickmenu-search-no-result').remove();
      cj('#overlay-quick-menu').append('<div id="quickmenu-search-no-result">No Match</div>');
      cj('#quickmenu-search-no-result').show();
    } else {
      cj('#quickmenu-search-no-result').remove();
    }
  });
}
