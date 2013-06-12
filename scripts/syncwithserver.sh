#!/bin/bash

# this will completely sync your machine with the actual server hosted on AWS (overwriting what you have)

rsync -avr ubuntu@socialer.dyndns.org:/var/www/ /var/www