import RPi.GPIO as GPIO            # import RPi.GPIO module
from time import sleep             # lets us have a delay
GPIO.setmode(GPIO.BCM)             # choose BCM or BOARD
GPIO.setup(23, GPIO.OUT)           # set GPIO23 as an output
GPIO.setup(24, GPIO.OUT)           # set GPIO24 as an output

GPIO.output(23, 0)         # set GPIO23 to 0/GPIO.LOW/False
GPIO.output(24, 0)         # set GPIO24 to 0/GPIO.LOW/False
sleep(30)                 # wait 30 seconds
GPIO.output(23, 1)         # set GPIO23 to 1/GPIO.HIGH/True
GPIO.output(24, 1)         # set GPIO24 to 1/GPIO.HIGH/True
#sleep(5)                 # wait half a second

GPIO.cleanup()
