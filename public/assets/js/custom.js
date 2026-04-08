/* Add here all your JS customizations */

window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        // The page was loaded from the cache
        window.location.reload();
    }
});

$(window).on('load', function() {
    // Hide the loader once the page is fully loaded
    $('#loader').addClass('hidden');
});


document.querySelectorAll('.cust-textarea').forEach(function(textarea) {
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.stopPropagation(); // Prevents the Enter key from propagating to other elements
        }
    });
});


function convertCurrencyToWords(number) {
    const Thousand = 1000;
    const Million = Thousand * Thousand;
    const Billion = Thousand * Million;
    const Trillion = Thousand * Billion;

    if (number === 0) return "Zero Rupees Only";
    
    const isNegative = number < 0;
    number = Math.abs(number);

    let result = "";

    // Trillions
    if (number >= Trillion) {
        result += convertDigitGroup(Math.floor(number / Trillion)) + " Trillion ";
        number %= Trillion;
    }

    // Billions
    if (number >= Billion) {
        result += convertDigitGroup(Math.floor(number / Billion)) + " Billion ";
        number %= Billion;
    }

    // Millions
    if (number >= Million) {
        result += convertDigitGroup(Math.floor(number / Million)) + " Million ";
        number %= Million;
    }

    // Thousands
    if (number >= Thousand) {
        result += convertDigitGroup(Math.floor(number / Thousand)) + " Thousand ";
        number %= Thousand;
    }

    // Hundreds and below
    if (number > 0) {
        result += convertDigitGroup(number);
    }

    result = result.trim() + " Rupees Only";
    
    // Handle negative case
    return isNegative ? "Negative " + result : result;
}

function convertDigitGroup(number) {
    const hundreds = Math.floor(number / 100);
    const remainder = number % 100;
    let result = "";

    if (number === 1) {
        return "One";
    }
    if (number === 2) {
        return "Two";
    }
    if (number === 3) {
        return "Three";
    }
    if (number === 4) {
        return "Four";
    }
    if (number === 5) {
        return "Five";
    }
    if (number === 6) {
        return "Six";
    }
    if (number === 7) {
        return "Seven";
    }
    if (number === 8) {
        return "Eight";
    }
    if (number === 9) {
        return "Nine";
    }
    
    if (hundreds > 0) {
        result += convertSingleDigit(hundreds) + " Hundred ";
    }

    if (remainder > 0) {
        if (remainder < 20) {
            result += convertTens(remainder);
        } else {
            result += convertTens(Math.floor(remainder / 10) * 10);
            if (remainder % 10 > 0) {
                result += "-" + convertSingleDigit(remainder % 10);
            }
        }
    }

    return result.trim();
}

function convertSingleDigit(digit) {
    const digits = {
        0: "",
        1: "One",
        2: "Two",
        3: "Three",
        4: "Four",
        5: "Five",
        6: "Six",
        7: "Seven",
        8: "Eight",
        9: "Nine"
    };

    return digits[digit];
}

function convertTens(number) {
    const tens = {
        10: "Ten",
        11: "Eleven",
        12: "Twelve",
        13: "Thirteen",
        14: "Fourteen",
        15: "Fifteen",
        16: "Sixteen",
        17: "Seventeen",
        18: "Eighteen",
        19: "Nineteen",
        20: "Twenty",
        30: "Thirty",
        40: "Forty",
        50: "Fifty",
        60: "Sixty",
        70: "Seventy",
        80: "Eighty",
        90: "Ninety"
    };

    return tens[number] || "";
}

/* ===========================
   CONFIGURATION
=========================== */

const timeoutWarning  = 28 * 60 * 1000; // 28 minute (change as needed)
const timeoutRedirect = 30 * 60 * 1000; // 30 minutes (change as needed)

/* ===========================
   VARIABLES
=========================== */
let warningTimeout  = null;
let redirectTimeout = null;

/* ===========================
   SHARED BROADCAST CHANNEL
=========================== */
const activityChannel = new BroadcastChannel('session_activity');

/* ===========================
   RESET TIMER (USER ACTIVE)
=========================== */
function resetTimer() {
    const now = Date.now();

    // Save activity time
    localStorage.setItem('lastActivityTime', now);

    // Notify other tabs
    activityChannel.postMessage({
        type: 'activity',
        timestamp: now
    });

    initializeTimers();
}

/* ===========================
   INITIALIZE / RE-CALCULATE
=========================== */
function initializeTimers() {
    clearTimeout(warningTimeout);
    clearTimeout(redirectTimeout);

    const lastActivityTime = parseInt(localStorage.getItem('lastActivityTime') || Date.now());
    const now = Date.now();
    const timeElapsed = now - lastActivityTime;

    if (timeElapsed >= timeoutRedirect) {
        expireSession();
        return;
    }

    if (timeElapsed >= timeoutWarning) {
        const remaining = timeoutRedirect - timeElapsed;

        warningTimeout  = setTimeout(showModal, 0);
        redirectTimeout = setTimeout(expireSession, remaining);
        return;
    }

    warningTimeout  = setTimeout(showModal, timeoutWarning - timeElapsed);
    redirectTimeout = setTimeout(expireSession, timeoutRedirect - timeElapsed);
}

/* ===========================
   SHOW WARNING (ACTIVE TAB ONLY)
=========================== */
function showModal() {
    if (document.visibilityState === 'visible') {
        $('#timeoutModal').fadeIn();
    }
}

/* ===========================
   EXPIRE SESSION
=========================== */
function expireSession() {
    fetch('/logout', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => window.location.href = '/login')
    .catch(err => console.error('Session expire error:', err));
}

/* ===========================
   CONTINUE SESSION BUTTON
=========================== */
$('#continueSession').on('click', function () {
    fetch('/keep-alive', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(() => {
        $('#timeoutModal').hide();
        resetTimer();
    });
});

/* ===========================
   LOGOUT BUTTON
=========================== */
$('#logoutSession').on('click', expireSession);

/* ===========================
   RECEIVE ACTIVITY FROM TABS
=========================== */
activityChannel.onmessage = (event) => {
    if (event.data.type === 'activity') {
        localStorage.setItem('lastActivityTime', event.data.timestamp);
        initializeTimers();
    }
};

/* ===========================
   TAB VISIBILITY CHANGE
=========================== */
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
        initializeTimers();
    }
});

/* ===========================
   USER ACTIVITY LISTENER
=========================== */
let throttle;
$(document).on('mousemove keypress click scroll', function () {
    clearTimeout(throttle);
    throttle = setTimeout(resetTimer, 300);
});

/* ===========================
   START ON PAGE LOAD
=========================== */
resetTimer();
initializeTimers();

document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
    // Prevent the default form submission
    event.preventDefault();

    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_new_password').value;
    const currentPassword = document.getElementById('current_password').value;

    // Basic validation to ensure all fields are filled
    if (!currentPassword || !newPassword || !confirmPassword) {
        alert('All fields are required.');
        return false; // Stop execution
    }

    // CSRF Token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // AJAX request to validate current password
    $.ajax({
        type: 'GET',
        url: '/validate-user-password/',
        data: { 'password': currentPassword },
        success: function (response) {
            if (response == 1) {
                // Check if newPassword matches confirmPassword
                if (newPassword !== confirmPassword) {
                    alert('New Password and Confirm New Password do not match.');
                    return false; // Stop execution
                } else {
                    // Submit the form programmatically if everything is valid
                    const form = document.getElementById('changePasswordForm');
                    form.submit();
                }
            } else if (response == 0) {
                alert("Current Password is not correct.");
            }
        },
        error: function () {
            alert("An error occurred while validating the current password.");
        }
    });

    // Return false to prevent form submission by default
    return false;
});