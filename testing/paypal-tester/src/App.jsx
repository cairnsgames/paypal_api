import React, { useState } from "react";
import ReactDOM from "react-dom/client";

import "./index.css";
import PayNowButton from "./paynowbutton";

import { PayPalScriptProvider } from "@paypal/react-paypal-js";

const initialOptions = {
  clientId:
    "Ab0yjA8p7PebhbjRYAr7T1_F2tvN9Rq2B2DH-4Jh9D3EU3nEaG3oRoDJm0aSlWY_Tty1tqr6CbnBLqAr",
  currency: "USD",
};

const App = () => {
  const [paid, setPaid] = useState(false);
  return (
    <PayPalScriptProvider options={initialOptions}>
      <div className="container">
        <div>Name: paypal-tester</div>
        <div>Framework: react</div>
        <div>Language: JavaScript</div>
        <div>CSS: Empty CSS</div>
        {!paid && (
          <PayNowButton
            orderId="123"
            totalPrice="100"
            onPaid={() => setPaid(true)}
          />
        )}
      </div>
    </PayPalScriptProvider>
  );
};
const rootElement = document.getElementById("app");
if (!rootElement) throw new Error("Failed to find the root element");

const root = ReactDOM.createRoot(rootElement);

root.render(<App />);
