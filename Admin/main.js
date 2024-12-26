// Function to open a modal by id
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

// Function to close a modal by id
function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
    }
}


// // Event listeners for popular and all package buttons
document.addEventListener('DOMContentLoaded', function () {
    // Toggle between popular packages and all packages
    document.querySelector('.btn-field .packbutton.popular').addEventListener('click', () => {
        document.querySelector('.allpack').style.display = 'none';
        document.querySelector('.popularpack').style.display = 'grid';

    });

    document.querySelector('.btn-field .packbutton.packall').addEventListener('click', () => {
        document.querySelector('.allpack').style.display = 'grid';
        document.querySelector('.popularpack').style.display = 'none';
    });

   

    // // Open and close modal for adding popular package
    // document.querySelectorAll('.add-popular a').forEach(item => {
    //     item.addEventListener('click', function(event) {
    //         event.preventDefault();
    //         openModal('myModal');
    //     });
    // });

    // document.querySelector('#myModal .close').addEventListener('click', () => {
    //     closeModal('myModal');
    // });

    // window.addEventListener('click', (event) => {
    //     if (event.target == document.getElementById('myModal')) {
    //         closeModal('myModal');
    //     }
    // });

    // // Open and close modal for adding all packages
    // document.querySelector('.button.addallpack').addEventListener('click', () => {
    //     openModal('myModalAll');
    // });

    // document.querySelector('#myModalAll .close').addEventListener('click', () => {
    //     closeModal('myModalAll');
    // });

    // window.addEventListener('click', (event) => {
    //     if (event.target == document.getElementById('myModalAll')) {
    //         closeModal('myModalAll');
    //     }
    // });




});

var popularpackbtn = document.querySelector('.popular');
var allpackbtn = document.querySelector('.packall');

popularpackbtn.addEventListener('click', () => {
    popularpackbtn.style.backgroundColor = '#d66500'; 
    allpackbtn.style.backgroundColor = '#333'; 
    
});

allpackbtn.addEventListener('click', () => {
    popularpackbtn.style.backgroundColor = '#333';
    allpackbtn.style.backgroundColor = '#d66500';
    
});