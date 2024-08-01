// /src/components/PayNowButton.js

import React, { useEffect } from "react";
import { PayPalButtons } from "@paypal/react-paypal-js";

const PayNowButton = ({ orderId, totalPrice, onPaid }) => {
  useEffect(() => {
    console.log("orderId", orderId);
    console.log("totalPrice", totalPrice);
  }, []);

  const createOrder = async (data, actions) => {
    console.log("createOrder", data, actions);
    try {
      // Send request to your PHP backend to create the PayPal order
      const response = await fetch(
        "http://localhost/cairnsgames/paypal/create.php",
        {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            order_id: orderId,
            total_price: totalPrice,
          }),
        }
      );

      if (!response.ok) {
        throw new Error("Network response was not ok.");
      }

      const { eccode, paymentid, error } = await response.json();

      if (error) {
        throw new Error(error);
      }

      return eccode; // Return the eccode to the PayPal Buttons component
    } catch (error) {
      console.error("Error creating PayPal order:", error);
      // Handle error state in your component if needed
      return Promise.reject(error); // Ensure the PayPal Buttons handle the error properly
    }
  };

  const onApprove = async (data, actions) => {
    try {
      // Handle the approval of the payment
      console.log("Payment approved:", data);
      if (onPaid) {
        onPaid();
      }

      // You might want to update the app state or notify the user here
      // For example, redirect to a success page or display a confirmation message
    } catch (error) {
      console.error("Error handling PayPal approval:", error);
    }
  };

  return (
    <div>
      <PayPalButtons
        createOrder={createOrder}
        onApprove={onApprove}
        onError={(error) => {
          console.error("PayPal Button Error:", error);
          // Handle the error case, like displaying an error message
        }}
      />
    </div>
  );
};

export default PayNowButton;
