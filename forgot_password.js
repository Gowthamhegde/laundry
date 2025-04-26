// Initialize EmailJS with your User ID
emailjs.init('yMHuGT7IsQdYzpuoc');

// DOM Elements
const emailForm = document.getElementById('email-form');
const otpContainer = document.getElementById('otp-container');
const resetForm = document.getElementById('reset-form');
const messageDiv = document.getElementById('message');
const emailInput = document.getElementById('email');
const otpInput = document.getElementById('otp');
const newPasswordInput = document.getElementById('new-password');
const confirmPasswordInput = document.getElementById('confirm-password');
const sendOtpBtn = document.getElementById('send-otp-btn');
const verifyOtpBtn = document.getElementById('verify-otp-btn');
const resetPasswordBtn = document.getElementById('reset-password-btn');
const resendOtpLink = document.getElementById('resend-otp');

// Store OTP in memory (in a real app, you might want to use a more secure method)
let generatedOtp = '';
let userEmail = '';

// Show message function
function showMessage(text, isError = false) {
    messageDiv.textContent = text;
    messageDiv.className = isError ? 'message error' : 'message success';
    messageDiv.style.display = 'block';
    
    // Hide message after 5 seconds
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Generate 6-digit OTP
function generateOtp() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

// Send OTP via EmailJS
async function sendOtpEmail(email) {
    generatedOtp = generateOtp();
    userEmail = email;
    
    try {
        console.log("Attempting to send OTP to:", email);
       // console.log("Using service ID:", serviceID);
        //console.log("Using template ID:", templateID);
        const response = await emailjs.send('service_odhv81g', 'template_jhwh6oi', {
            to_email: email,
            passcode: generatedOtp,
            from_name: 'laundry techs'
        });
        
        return { success: true, message: 'OTP sent successfully' };
    } catch (error) {
        console.error('Failed to send OTP:', error);
        return { success: false, message: 'Failed to send OTP. Please try again.' };
    }
}

// Event Listeners
sendOtpBtn.addEventListener('click', async () => {
    const email = emailInput.value.trim();
    
    if (!email) {
        showMessage('Please enter your email address', true);
        return;
    }
    
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showMessage('Please enter a valid email address', true);
        return;
    }
    
    sendOtpBtn.disabled = true;
    sendOtpBtn.textContent = 'Sending...';
    
    const result = await sendOtpEmail(email);
    
    if (result.success) {
        showMessage('OTP has been sent to your email');
        emailForm.style.display = 'none';
        otpContainer.style.display = 'block';
    } else {
        showMessage(result.message, true);
    }
    
    sendOtpBtn.disabled = false;
    sendOtpBtn.textContent = 'Send OTP';
});

verifyOtpBtn.addEventListener('click', () => {
    const enteredOtp = otpInput.value.trim();
    
    if (!enteredOtp) {
        showMessage('Please enter the OTP', true);
        return;
    }
    
    if (enteredOtp.length !== 6 || !/^\d+$/.test(enteredOtp)) {
        showMessage('OTP must be 6 digits', true);
        return;
    }
    
    if (enteredOtp === generatedOtp) {
        showMessage('OTP verified successfully');
        otpContainer.style.display = 'none';
        resetForm.style.display = 'block';
    } else {
        showMessage('Invalid OTP. Please try again.', true);
    }
});

resendOtpLink.addEventListener('click', async (e) => {
    e.preventDefault();
    
    if (!userEmail) {
        showMessage('No email address found', true);
        return;
    }
    
    resendOtpLink.textContent = 'Sending...';
    
    const result = await sendOtpEmail(userEmail);
    
    if (result.success) {
        showMessage('New OTP has been sent to your email');
    } else {
        showMessage(result.message, true);
    }
    
    resendOtpLink.textContent = 'Resend OTP';
});

resetPasswordBtn.addEventListener('click', () => {
    const newPassword = newPasswordInput.value;
    const confirmPassword = confirmPasswordInput.value;
    
    if (!newPassword || !confirmPassword) {
        showMessage('Please enter and confirm your new password', true);
        return;
    }
    
    if (newPassword.length < 8) {
        showMessage('Password must be at least 8 characters', true);
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showMessage('Passwords do not match', true);
        return;
    }
    
    // In a real application, you would send this to your server
    // to actually update the password in your database
    showMessage('Password reset successfully!');
    
    // Reset the form
    setTimeout(() => {
        resetForm.style.display = 'none';
        emailForm.style.display = 'block';
        emailInput.value = '';
        otpInput.value = '';
        newPasswordInput.value = '';
        confirmPasswordInput.value = '';
    }, 2000);
});