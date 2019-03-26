<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileHandlingController extends Controller
{
    private $path='storage/';
    public function getFileFromUrl($request){
        // test: http://janelwashere.com/files/bible_daily.txt
        $request->validate([
            'url' => 'required|url'
        ]);
        //get URL header and test if it is existing URL and if the URL points to a text file
        $header = get_headers($request->url,1);
        if($header[0]!=="HTTP/1.1 404 Not Found"){
            if($header["Content-Type"]==="text/plain"){
                $filename = md5_file($request->url).".txt";
                copy($request->url,$this->path.$filename);
                return "".$filename;
            }else{
                return redirect()->back()->withErrors(['url'=>['The file extension must be .txt']]);
            }
        }else{
            return redirect()->back()->withErrors(['url'=>['The url must point to an existing file']]);
        }
    }
    public function getUploadedFile($request){
        $request->validate([
            'file' => 'required|file|mimes:txt|max:2048'
        ]);
        if($request->hasFile('file')){
            $filename = md5_file($request->file('file')).".txt";
            $request->file('file')->storeAs('public/',$filename);
        }
        return $filename;
    }
    public function getWordsFromString($string){
        //remove line breaks and unneccesary characters except: space, &
        //keep the next characters for different date formats ".-/"
        $string = preg_replace("/[^a-zA-Z0-9\ \&\.\/\-]/", "", $string);
        $words= explode(' ',$string);
        //remove dot from the end of words (end of sentence) but not from dates
        $words = array_map(
            function($element){
                return rtrim($element,'.');
            },
            $words
        );
        return $words;
    }
    public function averageWordLength($wordsLength){
        $sum = 0;
        $count=0;
        foreach($wordsLength as $length=>$times){
            $sum+=($length*$times);
            $count+=$times;
        }
        return round($sum/$count,3);
    }
    public function wordLengthFrequency($wordsLength){
        arsort($wordsLength);
        $res = "";
        $last_num=0;
        foreach($wordsLength as $key=>$value){
            if($last_num===0){
                $last_num=$value;
                $res=$key;
            }else{
                if($last_num===$value){
                    $res.=" & ".$key;
                }else{
                    break;
                }
            }
        }
        return $last_num.", for word lengths of ".$res;
    }
    public function __invoke(Request $request){
        if($request->has('url')){
            $filename = $this->getFileFromUrl($request);
        }else if($request->has('file')){
            $filename = $this->getUploadedFile($request);
        }else if($request->has('existing')){
            $filename = $request->existing;
        }else{
            return redirect('welcome');
        }
        if (file_exists($this->path.$filename) && $fh = fopen($this->path.$filename, 'r')){
            $wordCount =0;
            //$wordsLengthCount an array where Key = Length of words, Value = Frequency of word length
            $wordsLengthCount=[];
            while (!feof($fh)) {
                $line = fgets($fh);
                $words = $this->getWordsFromString($line);
                //remove empty elements from an array
                $words = array_filter($words);
                //if $words is empty when the line is just a line break then jump to the next line
                if(empty($words)) continue;
                foreach($words as $word){
                    $length = strlen($word);
                    if(array_key_exists($length,$wordsLengthCount)){
                        $wordsLengthCount[$length]++;
                    }else{
                        $wordsLengthCount[$length]=1;
                    }
                }
                //dump($words);
                $wordCount+=count($words); 
            }
            ksort($wordsLengthCount);
            fclose($fh);
            $return ="Word count = $wordCount <br>";
            $return.="Average word length = ".$this->averageWordLength($wordsLengthCount)."<br>";
            foreach($wordsLengthCount as $key =>$value){
                $return.="Number of words length $key is $value <br>";
            }
            $return.="The most frequently occurring word length is ".$this->wordLengthFrequency($wordsLengthCount);
            //dd($return);
            return redirect()->back()->with('return',$return);
        }
        return $filename;
    }
}
