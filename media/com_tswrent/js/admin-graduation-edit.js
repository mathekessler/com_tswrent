
  function calcDays(element){

      var parts = element.split("__"); 
      var firstPart = parts[0];   // jform_graduations
      var secondPart = parts[1];  // graduations0
      var thirdPart = parts[2];    // duration
      var id = "jform_graduations__";
      id =id+secondPart+'__';
      const id_duration = id+'duration'; 
      const id_unit = id+'unit'; 
      const id_days= id+'days';
      
      const duration= document.getElementById(id_duration).value;
      const unit= document.getElementById(id_unit).value;
      let factor= (unit == 0) ?  7 : unit;
      const fullDays = duration*factor;

      document.getElementById(id_days).value = fullDays;
      checkDays();
  }


  function checkDays(){

      let count = document.querySelectorAll('[data-base-name="graduations"]');
      count = count.length;
      const foundElements = [];
      for (let i = 0; i < count; i++) {
          const element = document.getElementById('jform_graduations__graduations'+i+'__days');
          if (element) {
              foundElements.push(element.value);
          }
      }
      const duplicateIndex = findDuplicatesWithIndices(foundElements)
      
      if (duplicateIndex.length > 0){
        Joomla.renderMessages({
        error: [Joomla.Text._('COM_TSWRENT_GRADUATION_DUPLICATE_ENTRIES')]
        });
        
      }
      

      // check for dublicated entries in array and show this index
  }

  function findDuplicatesWithIndices(arr) {
      const seen = {};
      const duplicates = [];
    
      for (let i = 0; i < arr.length; i++) {
        const item = arr[i];
        if (seen[item]) {
          if (seen[item] === 1) {
            const duplicateEntry = [i - 1];
            duplicates.push(duplicateEntry);
          }
          const lastDuplicate = duplicates[duplicates.length - 1];
          lastDuplicate.push(i);
          seen[item]++;
        } else {
          seen[item] = 1;
        }
      }
      const nestedArray = duplicates;
      const flatArray = [].concat.apply([], nestedArray);

      return flatArray;
    }



/* 
  <joomla-alert type="danger" close-text="Close" dismiss="true" style="animation-name: joomla-alert-fade-in;" role="alert">
    <button type="button" class="joomla-alert--close" aria-label="Close">
      <span aria-hidden="true">×</span>
    </button>
    <div class="alert-heading">
      <span class="error"></span><span class="visually-hidden">Error</span>
    </div>
    <div class="alert-wrapper">
      <div class="alert-message">Please correct the marked fields and try again.</div>
    </div>
  </joomla-alert> */