package eu.smartcontroller.guard.demo.model;

public class Time {
    long value;
    int value2;

    public Time()
    {
        value = getCurrentTime();
    }

    //getter and setter for value
    public long getCurrentTime()
    {
        return System.currentTimeMillis();
    }

    public int getValue2() {
        return value2;
    }

    public void setValue2(int value2) {
        this.value2 = value2;
    }
}
