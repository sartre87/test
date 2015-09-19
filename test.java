/*
 * File Name: DriverGUI.java
 * 
 * Description: 
 * Statistics Program - Graphic User Interface
 * Allows the user to input a series of numbers, then displays
 * the average, max, min, and total of the inputted numbers.
 */

import javax.swing.*;
import java.awt.*;
import java.awt.event.*;

public class DriverGUI extends JFrame implements ActionListener {
    // DATA MEMBERS
    private static NumberList numList = new NumberList();
    private static String     numStr  = "";
    
    // GLOBAL CONSTANTS
    private static final String DEFAULT_MSG = "Input Number and Press Enter";
    
    // GUI SETTINGS
    private static final String TITLE         = "Stats Window";
    private static final int    WINDOW_WIDTH  = 340;
    private static final int    WINDOW_HEIGHT = 280;
    private static final Font   DEFAULT_FONT  = 
            new Font("Consolas", Font.BOLD, 15);
    
    // GUI COMPONENTS
    private static JTextField  input      = new JTextField(10);
    private static JLabel      inputLBL   = new JLabel("Input #: ");
    private static JTextArea   output     = new JTextArea(11,40);  
    private static JButton     calcBTN    = new JButton("CALC ");
    private static JButton     clearBTN   = new JButton("CLEAR");
    
    /*
     * Constructor
     */
    DriverGUI(){
        // Set up the GUI
        Container frameContainer = getContentPane();
        frameContainer.setLayout(new FlowLayout(FlowLayout.CENTER));
        Color color = new Color(245,245,245);
        output.setEditable( false );
        output.setBackground(color);
        output.setAutoscrolls(true);
        output.setLineWrap(true);
        output.setWrapStyleWord(true);
        output.setText(DEFAULT_MSG);
                       
        // Add Key Listener to input field.
        input.addKeyListener( new KeyListener() {
            public void keyPressed (KeyEvent e) {   }

            public void keyReleased (KeyEvent e) {
                int key = e.getKeyCode();

                if ( key == KeyEvent.VK_ENTER && !input.getText().equals("") ) {
                    addNumber(input.getText());
                    input.setText("");
                }
            }

            public void keyTyped (KeyEvent e) {   }
        });
        
        // Set Up Button Action Listeners
        calcBTN.addActionListener(this);
        calcBTN.setActionCommand("calc");
        clearBTN.addActionListener(this);
        clearBTN.setActionCommand("clear");
        
        // Set GUI component fonts
        calcBTN.setFont(DEFAULT_FONT);
        clearBTN.setFont(DEFAULT_FONT);
        inputLBL.setFont(DEFAULT_FONT);
        input.setFont(DEFAULT_FONT);
        output.setFont(DEFAULT_FONT);
        
        // Add the components to the frame.
        add(inputLBL);
        add(input);
        add(calcBTN);
        add(clearBTN);
        add(output);
    }
    
    /*
     * Action Listener
     * para e ActionEvent
     */
    @Override public void actionPerformed(ActionEvent e) {
        // Calculate Button Pressed
        if( e.getActionCommand().equals("calc") )
            calculate();
        
        // Clear Button Pressed
        if( e.getActionCommand().equals("clear") )
            clear();
    }
    
    /*
     * Takes in a string containing user input. Checks to see if the string 
     * contains a valid number. If the input is valid, places the number in the
     * number list. If the input is invalid, display an error message.
     * @param num User Input Number
     */
    private static void addNumber(String num) {
        try {
            double userInput = Double.parseDouble(input.getText());
            
            // Update Number String
            if( "".equals(numStr) ) {
                numStr = "" + userInput;
            } else {
                numStr = numStr + ", " + userInput;
            }
            
            // Add User Input to the Number List
            numList.addNumber(userInput);
            
            // Update Output
            output.setText("Number added to the list.\n\n" + numStr);
        } catch ( NumberFormatException e ) {
            output.setText(" Invalid Input!\n\n" + numStr);
        }
    }
    
    /*
     * Calculate and display the max, min, and average, and total
     * of the number list.
     */
    private static void calculate() {
        String avg   = String.format("%4.1f",numList.getAverage());
        String max   = String.format("%4.1f",numList.getMin());
        String min   = String.format("%4.1f",numList.getMax());
        String total = String.format("%4.1f",numList.getTotal());
        
        output.setText("\n" + numStr + "\n\nStatistics:\n"
                + "\nAverage: " + avg
                + "\nMax:     " + max
                + "\nMin:     " + min
                + "\nTotal:   " + total );
    }
    
    /*
     * Clears the number list and all user input.
     */
    private static void clear() {
        input.setText("");
        output.setText(DEFAULT_MSG);
        numList.clearList();
        numStr = "";
    }
    
    /*
     * MAIN METHOD
     */    
    public static void main(String[] args) {
        DriverGUI frame = new DriverGUI();
        frame.setTitle(TITLE);
        WindowListener windowListener = new WindowAdapter() {
            public void windowClosing(WindowEvent e) { 
                System.exit(0);
            }
        };
        
        frame.addWindowListener(windowListener);
        frame.pack();
        frame.setSize(WINDOW_WIDTH,WINDOW_HEIGHT);
        frame.setResizable( false );
        frame.setVisible(true);
    }
}
