db.createCollection("products");

db.products.insertMany([
    { 
      name: "Power Bank", 
      price: 10, 
      description: "A compact and powerful USB charger, ideal for travelers and those who need reliable power on-the-go. Features fast charging capabilities with dual USB ports.",
      category: "Electronics",
      quantity: 100,
      visible: false,
      dateAdded: new Date()
    },
    { 
      name: "T-Shirt", 
      price: 20, 
      description: "Stylish and comfortable cotton t-shirt, available in various sizes and colors. Perfect for casual outings and light athletic activities.",
      category: "Clothing",
      quantity: 50,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Arthur Lives", 
      price: 30, 
      description: "Engaging contemporary novel by a bestselling author, exploring themes of adventure and self-discovery. A must-read for book lovers.",
      category: "Books",
      quantity: 150,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Bluetooth Headphones", 
      price: 25, 
      description: "High-quality sound with long-lasting battery life. Features noise cancellation and wireless connectivity.",
      category: "Electronics",
      quantity: 75,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Yoga Mat", 
      price: 15, 
      description: "Eco-friendly and non-slip yoga mat for all levels. Durable, lightweight, and available in multiple colors.",
      category: "Fitness",
      quantity: 120,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Cooking Pot Set", 
      price: 50, 
      description: "Premium stainless steel cooking pots with heat-resistant handles. Suitable for all stove types.",
      category: "Home Essentials",
      quantity: 40,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "LED Desk Lamp", 
      price: 18, 
      description: "Adjustable and dimmable desk lamp with multiple lighting modes. Perfect for reading or working.",
      category: "Furniture",
      quantity: 85,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Running Shoes", 
      price: 35, 
      description: "Lightweight and breathable running shoes with enhanced cushioning and durability for all runners.",
      category: "Footwear",
      quantity: 90,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Backpack", 
      price: 40, 
      description: "Durable and spacious backpack, ideal for both hiking and daily use. Includes multiple compartments and waterproof material.",
      category: "Accessories",
      quantity: 110,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Electric Kettle", 
      price: 22, 
      description: "Fast boiling electric kettle with automatic shut-off and boil-dry protection. Stainless steel body.",
      category: "Kitchen Appliances",
      quantity: 60,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Sketch Pad", 
      price: 12, 
      description: "High-quality sketch pad suitable for all types of drawing media. Includes 50 acid-free sheets.",
      category: "Art Supplies",
      quantity: 130,
      visible: true,
      dateAdded: new Date()
    },
    { 
      name: "Garden Hose", 
      price: 28, 
      description: "Expandable garden hose with a high-pressure water spray nozzle and durable material. Easy to store and use.",
      category: "Gardening",
      quantity: 65,
      visible: true,
      dateAdded: new Date()
    }
  ]);
  

