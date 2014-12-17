M.block_helpdesk = {

  init: function(Y) {
    
    this.Y = Y;
    var submit_btn = Y.one('#helpdesk_submit');
    
    submit_btn.on('click', function(e) {

      var form_id = 'helpdesk_form';
      var helpdesk_form = Y.one('#'+form_id);
    
      YUI().use("io-form", function(Y) {

        var url = helpdesk_form.get('action');
        var cfg = {
          method: 'POST',
          form: {
            id: form_id,
            useDisabled: true
          },
          on: {

            success: M.block_helpdesk.onsuccess,
            failure: M.block_helpdesk.onfailure

          }
        }

        // Start the transaction.
        var request = Y.io(url, cfg);
      })

      e.preventDefault();
      return false;
    })

  },

  onsuccess: function (o, response) {

    var Y = M.block_helpdesk.Y;
    //Y.log(response.responseText);

    try {
      var data = Y.JSON.parse(response.responseText);

    } catch (e) {

      //Y.log("JSON Parse failed!" + e);
      return;

    }
    if (data.result) {

        //remove existing div nodes
        var nodes = Y.one('.content1');
        nodes.all('div').remove();
        //show hidden nodes
        var new_node = Y.one('#helpdesk_success');
        new_node.show();

      }
  },
  onfailure: function (o, response) {

    var Y = M.block_helpdesk.Y;
    Y.log('Failure' + response.responseText);
    
    //remove existing div nodes
    var nodes = Y.one('.content1');
    nodes.all('div').remove();
    //show hidden nodes
    var new_node = Y.one('#helpdesk_failure');
    new_node.show();

  }

} //end block
