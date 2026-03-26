---
name: devtools-login
description: 'Use this skill to automate logging into the Laravel application using Chrome DevTools. Triggers when user asks to log in, authenticate, sign in, or test the app with authenticated session. Provides the complete workflow for navigating to the login page, filling credentials, and submitting the form. Covers: navigation, form filling, login submission, verification of successful login.'
license: MIT
metadata:
    author: assistant
---

# Chrome DevTools Login Automation

## Quick Reference

### Login Credentials

```
URL: http://localhost:8000/dashboard
Email: test@example.com
Password: password
```

### Login Workflow

```typescript
// 1. Navigate to the dashboard (will redirect to login if not authenticated)
await chrome_devtools_navigate_page({
    type: 'url',
    url: 'http://localhost:8000/dashboard',
});

// 2. Take a snapshot to see the login form elements
const snapshot = await chrome_devtools_take_snapshot({});

// 3. Fill the email input (look for uid containing 'email' in the snapshot)
await chrome_devtools_fill({
    uid: 'EMAIL_INPUT_UID',
    value: 'test@example.com',
});

// 4. Fill the password input
await chrome_devtools_fill({
    uid: 'PASSWORD_INPUT_UID',
    value: 'password',
});

// 5. Submit the form (click login button or press Enter)
await chrome_devtools_click({
    uid: 'LOGIN_BUTTON_UID',
});
// OR
await chrome_devtools_press_key({
    key: 'Enter',
});

// 6. Verify successful login by waiting for dashboard content
await chrome_devtools_wait_for({
    text: ['Dashboard', 'Welcome'],
});
```

## Full Login Function

```typescript
async function loginToApp() {
    // Navigate to dashboard
    await chrome_devtools_navigate_page({
        type: 'url',
        url: 'http://localhost:8000/dashboard',
    });

    // Take snapshot to find form elements
    const snapshot = await chrome_devtools_take_snapshot({});

    // Find email input (common patterns: 'email', 'Email', 'email_address')
    const emailInput = snapshot.elements.find(
        (el) =>
            el.name?.toLowerCase().includes('email') ||
            el.properties?.placeholder?.toLowerCase().includes('email') ||
            el.properties?.id?.toLowerCase().includes('email'),
    );

    // Find password input
    const passwordInput = snapshot.elements.find(
        (el) =>
            el.name?.toLowerCase().includes('password') ||
            el.properties?.placeholder?.toLowerCase().includes('password') ||
            el.properties?.type === 'password',
    );

    // Find login/submit button
    const loginButton = snapshot.elements.find(
        (el) =>
            el.name?.toLowerCase().includes('login') ||
            el.name?.toLowerCase().includes('sign in') ||
            el.name?.toLowerCase().includes('log in') ||
            el.properties?.type === 'submit',
    );

    if (!emailInput || !passwordInput) {
        throw new Error('Could not find login form elements');
    }

    // Fill credentials
    await chrome_devtools_fill({
        uid: emailInput.uid,
        value: 'test@example.com',
    });

    await chrome_devtools_fill({
        uid: passwordInput.uid,
        value: 'password',
    });

    // Submit login
    if (loginButton) {
        await chrome_devtools_click({ uid: loginButton.uid });
    } else {
        await chrome_devtools_press_key({ key: 'Enter' });
    }

    // Wait for dashboard to load
    await chrome_devtools_wait_for({
        text: ['Dashboard'],
        timeout: 5000,
    });

    console.log('Login successful!');
}
```

## Using Form Fill (Multiple Fields)

```typescript
// Fill both email and password at once
await chrome_devtools_fill_form({
    elements: [
        { uid: 'EMAIL_UID', value: 'test@example.com' },
        { uid: 'PASSWORD_UID', value: 'password' },
    ],
});

// Then click login button
await chrome_devtools_click({ uid: 'LOGIN_BUTTON_UID' });
```

## Verification Steps

1. After login, check for dashboard-specific elements:

    ```typescript
    const postLoginSnapshot = await chrome_devtools_take_snapshot({});
    const isLoggedIn = postLoginSnapshot.elements.some(
        (el) =>
            el.name?.includes('Dashboard') ||
            el.name?.includes('Logout') ||
            el.name?.includes('Profile'),
    );
    ```

2. Check URL changed from /login to /dashboard:
    ```typescript
    // Use list_pages to get current URL
    const pages = await chrome_devtools_list_pages({});
    const currentUrl = pages[0].url;
    ```

## Common Issues

- **CSRF Token**: Laravel forms include CSRF tokens automatically - no extra handling needed
- **Redirects**: Inertia/Fortify handle redirects automatically after login
- **Form not found**: Ensure the page has fully loaded before taking snapshot

## Tools Reference

- `chrome_devtools_navigate_page` - Navigate to URL
- `chrome_devtools_take_snapshot` - Get page structure with element UIDs
- `chrome_devtools_fill` - Fill a single input
- `chrome_devtools_fill_form` - Fill multiple inputs at once
- `chrome_devtools_click` - Click a button/link
- `chrome_devtools_press_key` - Press keyboard keys (Enter, Tab, etc.)
- `chrome_devtools_wait_for` - Wait for text to appear on page
