$(function(){
  $("#leftPanel").formation();
  $.formation.addInput({type:"text",labelValue:"First Name:",required:true, defaultValue:"Matt"});
  $.formation.addInput({type:"text",labelValue:"Last Name:",required:true});
  $.formation.addInput({type:"text",labelValue:"E-mail:",validation:'email',required:true});
  $.formation.addInput({type:"text",labelValue:"Age:",validation:"number"});
  $.formation.addRadios(["Mac","Linux","Windows","Ubuntu"],{
    required:true,
    labelValue:"Your favorite OS:",
    legend:"Operating Systems"
  });
  $.formation.addCheckboxes({"apples":"Apples","oranges":"Oranges","grapes":"Grapes"},{labelValue:"What do you like to eat?",required:false,legend:"Fruit!"});
  $.formation.addCaptcha({
    captchaQuestions: {
      '5 + 5 = ?' : '10',
      'What color is the sky?':'blue',
      '2 + 2 = ?' : '4'
    },
    required:true
  });
  $.formation.addTextarea({
    name:"my_textarea",
    labelValue:"Describe yourself",
    required:true,
    cols:"40",
    rows:"10"
  });
  $.formation.addSelect({'1':'Yes','2':'No'},{labelValue:'Do you like jQuery?',required:true});
  $.formation.setErrorMessages({number:"Please enter a number."});
  $.formation.addButton({value: "SUBMIT"});
  $.formation.addButton({value: "RESET",type:'reset'})
});
