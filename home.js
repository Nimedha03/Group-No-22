const words = [
    "Streamlining BICT lecture assets, database guides, and exam archives into one powerful space."
];
let wordIndex = 0;
let charIndex = 0;
let deleting = false;
const text = document.getElementById("text");
function typing(){
    let current = words[wordIndex];
    if(!deleting){
        text.textContent = current.substring(0,charIndex + 1);
        charIndex++;
        if(charIndex === current.length){
            deleting = true;
            setTimeout(typing,1000);
            return;
        }
    }
    else{
        text.textContent = current.substring(0,charIndex - 1);
        charIndex--;
        if(charIndex === 0){
            deleting = false;
            wordIndex++;
            if(wordIndex === words.length){wordIndex = 0 }
        }
    }
    setTimeout(typing,67);
}
typing();