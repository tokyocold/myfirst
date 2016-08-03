package main

import (
	"bytes"
	"encoding/binary"
	"math/rand"
	"io/ioutil"
	"fmt"
	"runtime"
	"os"
	"bufio"
	"io"
	"runtime/debug"
	"time"
)

func createByteFile(){
	byteFile:="/tmp/byteFile"
	var buffer bytes.Buffer

	for{
		if buffer.Len()>=1024*1024*1024{
			break
		}
		buffer.Write(uint64tobyte(uint64(rand.Int63())))
	}
	ioutil.WriteFile(byteFile,buffer.Bytes(),0644)
}
var bytecontent []byte
var err error

var sliceContent []uint64
var mapContent map[uint64]uint64

var mapContent2 map[keyStruct]uint64
type keyStruct struct {
	a uint64
	b uint64
}

func main(){
	memInfo()
	byteToMap2()
	debug.FreeOSMemory()
	fmt.Println(len(mapContent2))
	time.Sleep(10*time.Second)
	memInfo()
}

func byteRead(){
	// createByteFile()
	bytecontent,err=ioutil.ReadFile("/tmp/byteFile")
	if err!=nil{
		fmt.Println(err)
	}
	fmt.Println(len(bytecontent))
}

func byteToslice(){
	sliceContent=make([]uint64,0)
	fh,_:=os.Open("/tmp/byteFile")
	bio:=bufio.NewReader(fh)
	byte:=make([]byte,8*1000000)
	for{
		n,err:=bio.Read(byte)
		if err==io.EOF||err!=nil{
			break
		}
		for i:=0;i<n;i=i+8{
			sliceContent=append(sliceContent,bytetouint64(byte[0+i:8+i]))
		}
	}
}

//key---- struct
func byteToMap2(){
	mapContent2=make(map[keyStruct]uint64)
	fh,_:=os.Open("/tmp/byteFile")
	bio:=bufio.NewReader(fh)
	byte:=make([]byte,24*1000000)
	for{
		n,err:=bio.Read(byte)
		if err==io.EOF||err!=nil{
			break
		}
		for i:=0;i<n;i=i+24{
			mapContent2[keyStruct{bytetouint64(byte[0+i:8+i]),bytetouint64(byte[8+i:16+i])}] = bytetouint64(byte[16+i:24+i])
		}
	}
}

func byteToMap(){
	mapContent=make(map[uint64]uint64)
	fh,_:=os.Open("/tmp/byteFile")
	bio:=bufio.NewReader(fh)
	byte:=make([]byte,16*1000000)
	for{
		n,err:=bio.Read(byte)
		if err==io.EOF||err!=nil{
			break
		}
		for i:=0;i<n;i=i+16{
			mapContent[bytetouint64(byte[0+i:8+i])]=bytetouint64(byte[8+i:16+i])
		}
	}
}


func memInfo(){
	var m runtime.MemStats
	runtime.ReadMemStats(&m)
	fmt.Println("obtain:",m.Sys/1024/1024,"m")
	fmt.Println("useded:",m.Alloc/1024/1024,"m")
}

func uint64tobyte(num uint64) []byte{
	b:=make([]byte,8)
	binary.LittleEndian.PutUint64(b,num)
	return b
}

func bytetouint64(byte []byte) uint64{
	return binary.LittleEndian.Uint64(byte)
}
