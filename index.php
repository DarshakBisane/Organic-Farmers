<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Local Harvest - Farm Fresh Vegetables</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            padding-top: 100px;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Times New Roman', Times, serif;
        }

        .hero-section {
            background: #2e8b57;
            color: white;
            text-align: center;
            padding: 8rem 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .hero-content h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .featured-farmers h3 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 2rem;
        }

        .farmer-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .farmer-card h4 {
            font-size: 1.25rem;
            padding: 1rem 1rem 0;
        }

        .farmer-card p {
            color: #666;
            padding: 0 1rem;
        }

        .products-list span {
            background: #e9e9e9;
            color: #555;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .floating-results {
            animation: fadeIn 0.3s ease-in-out;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

    <!-- Firebase and LLM Import Scripts -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore, collection, query, where, getDocs, addDoc } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        import { setLogLevel } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";
        
        // IMPORTANT: Replace the placeholders below with your own Firebase and Gemini API keys.
        // Step-by-step instructions on how to get them are provided below the code block.
        const firebaseConfig = {
            apiKey: "YOUR_FIREBASE_API_KEY",
            authDomain: "YOUR_FIREBASE_AUTH_DOMAIN",
            projectId: "YOUR_FIREBASE_PROJECT_ID",
            storageBucket: "YOUR_FIREBASE_STORAGE_BUCKET",
            messagingSenderId: "YOUR_FIREBASE_MESSAGING_SENDER_ID",
            appId: "YOUR_FIREBASE_APP_ID"
        };
        const geminiApiKey = "YOUR_GEMINI_API_KEY";

        let app, db, auth;

        document.addEventListener('DOMContentLoaded', async () => {
            try {
                if (firebaseConfig.projectId) {
                    app = initializeApp(firebaseConfig);
                    auth = getAuth(app);
                    db = getFirestore(app);
                    setLogLevel('debug'); // To see Firestore logs in the console
                    
                    await signInAnonymously(auth);
                    console.log("Firebase initialized and signed in anonymously.");
                } else {
                    console.error("Firebase not initialized. Please add your firebaseConfig.");
                }
            } catch (error) {
                console.error("Error initializing Firebase:", error);
            }
        });

        const dbPath = 'public/data/items';

        window.addItem = async () => {
            if (!db) {
                console.error("Firestore not initialized.");
                return;
            }
            const items = [
                { name: 'Organic Tomatoes', price: 3.50, category: 'Vegetables', tags: ['tomato', 'vegetable', 'organic'] },
                { name: 'Fresh Spinach', price: 2.25, category: 'Greens', tags: ['spinach', 'green', 'leafy'] },
                { name: 'Heirloom Carrots', price: 4.00, category: 'Vegetables', tags: ['carrot', 'vegetable', 'root'] },
                { name: 'Red Bell Peppers', price: 1.75, category: 'Vegetables', tags: ['pepper', 'vegetable', 'bell'] },
                { name: 'Broccoli Florets', price: 3.00, category: 'Vegetables', tags: ['broccoli', 'vegetable', 'cruciferous'] },
                { name: 'Sweet Potatoes', price: 2.50, category: 'Root Vegetables', tags: ['potato', 'sweet', 'vegetable'] },
                { name: 'Garlic Bulbs', price: 1.00, category: 'Herbs', tags: ['garlic', 'bulb', 'herb'] },
                { name: 'Avocados', price: 1.50, category: 'Fruit', tags: ['avocado', 'fruit'] },
                { name: 'Cucumbers', price: 0.75, category: 'Vegetables', tags: ['cucumber', 'vegetable'] },
                { name: 'Lettuce', price: 2.00, category: 'Greens', tags: ['lettuce', 'green', 'leafy'] }
            ];

            const itemsRef = collection(db, dbPath);
            let count = 0;
            for (const item of items) {
                try {
                    const existingDocs = await getDocs(query(itemsRef, where('name', '==', item.name)));
                    if (existingDocs.empty) {
                        await addDoc(itemsRef, item);
                        count++;
                    }
                } catch (e) {
                    console.error("Error adding document: ", e);
                }
            }
            if (count > 0) {
                console.log(`Successfully added ${count} new items!`);
            } else {
                console.log("Items already exist. No new items were added.");
            }
        };

        const generateTextWithLLM = async (prompt) => {
            if (!geminiApiKey || geminiApiKey === "YOUR_GEMINI_API_KEY") {
                console.error("Gemini API key is not set.");
                return [];
            }
            const systemPrompt = `You are a helpful assistant. Given a search query, provide 3-5 short, relevant search suggestions related to the query. The suggestions should be practical and helpful for the user. Do not include a conversational introduction or conclusion. Just return a JSON array of strings.`;
            const userQuery = `Given the search query "${prompt}", suggest 3-5 related search terms or popular products.`;
            
            const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-05-20:generateContent?key=${geminiApiKey}`;

            const payload = {
                contents: [{ parts: [{ text: userQuery }] }],
                tools: [{ "google_search": {} }],
                systemInstruction: { parts: [{ text: systemPrompt }] },
                generationConfig: {
                    responseMimeType: "application/json",
                    responseSchema: {
                        type: "ARRAY",
                        items: { "type": "STRING" }
                    }
                }
            };
            
            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                
                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`API returned status ${response.status}: ${errorText}`);
                }
                
                const result = await response.json();
                const json = result?.candidates?.[0]?.content?.parts?.[0]?.text;
                if (json) {
                    return JSON.parse(json);
                }
                return [];
            } catch (error) {
                console.error('LLM API call failed:', error);
                return [];
            }
        };

        window.search = async (queryText) => {
            const resultsDiv = document.getElementById('search-results');
            const suggestionsDiv = document.getElementById('search-suggestions');
            const overlay = document.getElementById('search-overlay');
            
            if (!queryText.trim()) {
                resultsDiv.innerHTML = '';
                suggestionsDiv.innerHTML = '';
                overlay.classList.add('hidden');
                return;
            }

            overlay.classList.remove('hidden');
            resultsDiv.innerHTML = '<div class="p-4 text-center text-gray-500">Searching...</div>';
            suggestionsDiv.innerHTML = '<div class="p-4 text-center text-gray-500">Getting suggestions...</div>';

            try {
                if (!db) {
                    resultsDiv.innerHTML = `<div class="p-4 text-center text-red-500">Error: Firestore is not initialized. Check your Firebase config.</div>`;
                    suggestionsDiv.innerHTML = `<div class="p-4 text-center text-red-500">Error: Cannot get suggestions without Firebase.</div>`;
                    return;
                }
                
                const itemsRef = collection(db, dbPath);
                const q = query(itemsRef);
                const querySnapshot = await getDocs(q);
                
                const allItems = querySnapshot.docs.map(doc => ({ id: doc.id, ...doc.data() }));
                const filteredItems = allItems.filter(item => 
                    item.name.toLowerCase().includes(queryText.toLowerCase()) ||
                    (item.tags && item.tags.some(tag => tag.toLowerCase().includes(queryText.toLowerCase())))
                );
                
                resultsDiv.innerHTML = '';
                if (filteredItems.length > 0) {
                    resultsDiv.innerHTML = `
                        <div class="p-4 bg-white/50 backdrop-blur-sm rounded-md mb-2">
                            <h4 class="text-lg font-semibold mb-2">Search Results:</h4>
                            <div class="grid grid-cols-5 gap-2 text-sm font-bold text-gray-700 border-b pb-2">
                                <span>ID</span>
                                <span>Name</span>
                                <span>Price</span>
                                <span>Category</span>
                                <span>Tags</span>
                            </div>
                    `;
                    filteredItems.forEach(item => {
                        resultsDiv.innerHTML += `
                            <div class="grid grid-cols-5 gap-2 text-sm py-2 items-center border-b last:border-b-0">
                                <span class="truncate">${item.id}</span>
                                <span>${item.name}</span>
                                <span>$${item.price.toFixed(2)}</span>
                                <span>${item.category}</span>
                                <div class="flex flex-wrap gap-1">
                                    ${item.tags.map(tag => `<span class="px-2 py-0.5 bg-gray-200 rounded-full">${tag}</span>`).join('')}
                                </div>
                            </div>
                        `;
                    });
                    resultsDiv.innerHTML += `</div>`;
                } else {
                    resultsDiv.innerHTML = `<div class="p-4 text-center text-gray-500">No results found for "${queryText}".</div>`;
                }

                const suggestions = await generateTextWithLLM(queryText);
                suggestionsDiv.innerHTML = '';
                if (suggestions.length > 0) {
                    suggestionsDiv.innerHTML = `
                        <h4 class="text-md font-semibold text-gray-700 mb-2 mt-4">Suggestions:</h4>
                        <div class="flex flex-wrap gap-2">
                            ${suggestions.map(suggestion => `
                                <button onclick="document.getElementById('search-input').value='${suggestion}'; window.search('${suggestion}')" class="px-3 py-1 bg-white/50 backdrop-blur-sm text-sm text-gray-700 rounded-full hover:bg-white/80 transition-colors">
                                    ${suggestion}
                                </button>
                            `).join('')}
                        </div>
                    `;
                }
            } catch (error) {
                console.error("Error during search:", error);
                resultsDiv.innerHTML = `<div class="p-4 text-center text-red-500">An error occurred while searching.</div>`;
                suggestionsDiv.innerHTML = '';
            }
        };

        let debounceTimeout;
        window.onInput = (event) => {
            clearTimeout(debounceTimeout);
            const query = event.target.value;
            debounceTimeout = setTimeout(() => {
                window.search(query);
            }, 1000);
        };
    </script>

    <!-- Header and Navigation -->
    <header class="fixed top-0 left-0 w-full bg-white/90 backdrop-blur-md shadow-lg z-50 p-4 flex flex-col sm:flex-row justify-between items-center gap-4 sm:gap-8">
        <div class="logo">
            <a href="index.html"><h1 class="text-3xl sm:text-4xl text-[#2e8b57] font-bold">Local Harvest</h1></a>
        </div>
        <div class="w-full sm:w-80 relative">
            <input 
                type="text" 
                id="search-input" 
                oninput="onInput(event)"
                placeholder="Search for vegetables..."
                class="w-full px-4 py-2 rounded-full border-2 border-gray-300 shadow-sm focus:outline-none focus:border-[#2e8b57] transition-colors"
            >
            
            <div id="search-overlay" class="absolute top-full mt-2 w-full bg-white/60 backdrop-blur-md rounded-xl shadow-2xl p-4 overflow-hidden floating-results hidden">
                <div id="search-results" class="space-y-2">
                </div>
                <div id="search-suggestions" class="space-y-2">
                </div>
            </div>
        </div>
        <nav class="flex-grow">
            <ul class="flex justify-center sm:justify-end items-center gap-4 sm:gap-6">
                <li><a href="#" class="text-gray-600 font-bold hover:text-[#2e8b57] transition-colors">Home</a></li>
                <li><a href="#" class="text-gray-600 font-bold hover:text-[#2e8b57] transition-colors">Farmers</a></li>
                <li><a href="#" class="text-gray-600 font-bold hover:text-[#2e8b57] transition-colors">Login as a Farmer</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="w-full max-w-4xl px-4 sm:px-8">
        <section class="hero-section mt-8">
            <div class="hero-content">
                <h2>Fresh from the farm, to your table.</h2>
                <p>Find the best local, organic vegetables near you. Support your community and eat healthy!</p>
            </div>
        </section>

        <section class="featured-farmers text-center py-8">
            <h3>Featured Farmers</h3>
            <div class="farmer-cards-container flex flex-wrap justify-center gap-8">
                <div class="farmer-card bg-white rounded-xl shadow-md overflow-hidden w-full sm:w-80 transition-transform hover:scale-105">
                    <img src="https://placehold.co/400x200/2e8b57/ffffff?text=Green+Valley+Farms" alt="Farmer's image" class="w-full h-48 object-cover">
                    <div class="p-4 text-left">
                        <h4 class="text-xl font-semibold">Green Valley Farms</h4>
                        <p class="text-gray-600 mt-1"><i class="fas fa-map-marker-alt text-[#2e8b57]"></i> 5 km away</p>
                        <div class="products-list mt-4 flex flex-wrap gap-2">
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Carrots</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Potatoes</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Onions</span>
                        </div>
                    </div>
                </div>

                <div class="farmer-card bg-white rounded-xl shadow-md overflow-hidden w-full sm:w-80 transition-transform hover:scale-105">
                    <img src="https://placehold.co/400x200/2e8b57/ffffff?text=Sunny+Meadow+Produce" alt="Farmer's image" class="w-full h-48 object-cover">
                    <div class="p-4 text-left">
                        <h4 class="text-xl font-semibold">Sunny Meadow Produce</h4>
                        <p class="text-gray-600 mt-1"><i class="fas fa-map-marker-alt text-[#2e8b57]"></i> 8 km away</p>
                        <div class="products-list mt-4 flex flex-wrap gap-2">
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Tomatoes</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Spinach</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Lettuce</span>
                        </div>
                    </div>
                </div>

                <div class="farmer-card bg-white rounded-xl shadow-md overflow-hidden w-full sm:w-80 transition-transform hover:scale-105">
                    <img src="https://placehold.co/400x200/2e8b57/ffffff?text=Happy+Harvest+Farm" alt="Farmer's image" class="w-full h-48 object-cover">
                    <div class="p-4 text-left">
                        <h4 class="text-xl font-semibold">Happy Harvest Farm</h4>
                        <p class="text-gray-600 mt-1"><i class="fas fa-map-marker-alt text-[#2e8b57]"></i> 12 km away</p>
                        <div class="products-list mt-4 flex flex-wrap gap-2">
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Cabbage</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Broccoli</span>
                            <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm">Cauliflower</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="flex justify-center py-8">
            <button onclick="addItem()" class="px-6 py-3 bg-[#2e8b57] text-white font-semibold rounded-lg shadow-md hover:bg-[#256e43] transition-colors">
                Add Sample Vegetables to Database
            </button>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center py-4">
        <p>&copy; 2025 Local Harvest. All rights reserved.</p>
    </footer>

</body>

<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/12.3.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.3.0/firebase-analytics.js";
  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyCuQgnElP6Inja2a6TS18o1Prmhk_My-dU",
    authDomain: "organic-farming-43f77.firebaseapp.com",
    projectId: "organic-farming-43f77",
    storageBucket: "organic-farming-43f77.firebasestorage.app",
    messagingSenderId: "333461364141",
    appId: "1:333461364141:web:1723a74bfd7002f303d524",
    measurementId: "G-4C0RPX3E18"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
</script>
</html>
