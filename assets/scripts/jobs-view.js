document.addEventListener( 'DOMContentLoaded', function(){

  fetch(  window.location.origin + '/wp-content/uploads/paylocity/jobs.json')
    .then( response => response.json() )
    .then( data => displayJobs(data) )
    .catch( error => displayError(error) );

  function displayJobs( data ) {
    let container = document.querySelectorAll('.pjs_container')[0];

    if(container.length < 1) {
      return;
    }

    let $ul = document.createElement('UL');

    data.forEach( job => {
      let $li = document.createElement('LI');

      let title = document.createTextNode(job.JobTitle);
      let $anchor = document.createElement('A');
      $anchor.classList.add('pjs_job-title');
      $anchor.href = `https://recruiting.paylocity.com/Recruiting/Jobs/Details/${job.JobId}`;
      $anchor.target = '_blank';
      $anchor.appendChild( title );
      $li.appendChild( $anchor );

      let $div_details = document.createElement('DIV');
      $div_details.classList.add('pjs_job-details');

      if (job.HiringDepartment) {
        let department = document.createTextNode(job.HiringDepartment);
        let $span = document.createElement('SPAN');
        $span.appendChild(department);
        $div_details.appendChild($span);
      }

      if (job.LocationName) {
        let spacer = job.HiringDepartment ? '\u0020\u2013\u0020' : '';
        let location = document.createTextNode(spacer + job.LocationName);
        let $span = document.createElement('SPAN');
        $span.appendChild(location);
        $div_details.appendChild($span);
      }

      $li.appendChild( $div_details );
      $ul.appendChild( $li );
    });

    container.appendChild($ul);

  }

  function displayError( error ) {
    let container = document.querySelectorAll('.pjs_container')[0];

    if(!container.length == 1) {
      return;
    }

    console.log('error');

  }

});
